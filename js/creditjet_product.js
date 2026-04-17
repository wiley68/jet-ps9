jQuery(document).ready(function ($) {
  let card;
  card = 0;
  let jetBindingsInitialized = false;
  let jetProductPriceRefreshTimer = null;
  let jetProductPriceRefreshTimer2 = null;
  let jetProductPriceRefreshTimer3 = null;
  let jetRelinkQueued = false;
  const JET_BUTTON_SCOPE = ".creditjet-product-button-scope";
  const JET_POPUP_SCOPE = ".creditjet-product-popup-scope";
  const JET_POPUP_SELECTOR =
    "[id='jet-product-popup-container'], " + JET_POPUP_SCOPE;
  const JET_BUTTON_SELECTOR =
    "[id='jet-product-button-container'], " + JET_BUTTON_SCOPE;

  const jetKeepSingleContainer = (selector, preferInsideProductContainer) => {
    const $all = $(selector);
    if ($all.length === 0) {
      return null;
    }

    let $keep = $all.last();
    if (preferInsideProductContainer) {
      const $productHost = $(
        ".product-container, .js-product-container",
      ).first();
      if ($productHost.length) {
        const $inside = $all.filter(function () {
          return $.contains($productHost[0], this);
        });
        if ($inside.length) {
          $keep = $inside.last();
        }
      }
    }

    $all.not($keep).remove();
    return $keep;
  };

  const jetGetActivePopup = () => {
    const $popup = $(JET_POPUP_SELECTOR).last();
    return $popup.length ? $popup : $();
  };

  const jetShowActivePopup = () => {
    $(JET_POPUP_SELECTOR).hide();
    const $activePopup = jetGetActivePopup();
    if ($activePopup.length) {
      $activePopup.show();
    }
  };

  const jetResetPopupSteps = () => {
    $("#jet_step_1").show();
    $("#jet_step_2").hide();
  };

  const jetRelinkProductDom = () => {
    const $popup = jetKeepSingleContainer(JET_POPUP_SELECTOR, true);
    if ($popup && $popup.length) {
      // Normalize in case old markup variant is missing scope class/id.
      $popup.addClass("creditjet-product-popup-scope");
      $popup.attr("id", "jet-product-popup-container");
      // Avoid self-triggered mutation loop: move only if parent is not body.
      if ($popup.parent().get(0) !== document.body) {
        $popup.prependTo("body");
      }
    }

    const $buttons = jetKeepSingleContainer(JET_BUTTON_SELECTOR, true);
    if ($buttons && $buttons.length) {
      $buttons.addClass("creditjet-product-button-scope");
      $buttons.attr("id", "jet-product-button-container");
    }
  };

  const queueJetRelinkProductDom = () => {
    if (jetRelinkQueued) {
      return;
    }
    jetRelinkQueued = true;
    requestAnimationFrame(function () {
      jetRelinkQueued = false;
      jetRelinkProductDom();
    });
  };

  const scheduleJetProductRefresh = () => {
    if (jetProductPriceRefreshTimer !== null) {
      clearTimeout(jetProductPriceRefreshTimer);
    }
    jetProductPriceRefreshTimer = setTimeout(function () {
      jetProductPriceRefreshTimer = null;
      if (!document.getElementById("jet_price")) {
        return;
      }
      queueJetRelinkProductDom();
      jetCalculate();
    }, 120);
  };

  // Some themes update variant/price in multiple async passes.
  const scheduleJetProductRefreshBurst = () => {
    scheduleJetProductRefresh();

    if (jetProductPriceRefreshTimer2 !== null) {
      clearTimeout(jetProductPriceRefreshTimer2);
    }
    if (jetProductPriceRefreshTimer3 !== null) {
      clearTimeout(jetProductPriceRefreshTimer3);
    }

    jetProductPriceRefreshTimer2 = setTimeout(function () {
      jetProductPriceRefreshTimer2 = null;
      scheduleJetProductRefresh();
    }, 280);

    jetProductPriceRefreshTimer3 = setTimeout(function () {
      jetProductPriceRefreshTimer3 = null;
      scheduleJetProductRefresh();
    }, 550);
  };

  const jetExtractNumericPriceStringFromDisplayText = (text) => {
    if (text == null || typeof text !== "string") {
      return "";
    }
    let s = text
      .replace(/\u00a0/gi, " ")
      .replace(/\s+/g, "")
      .replace(/[^\d.,-]/g, "");
    if (!s) {
      return "";
    }
    const hasComma = s.indexOf(",") !== -1;
    const hasDot = s.indexOf(".") !== -1;
    if (hasComma && hasDot) {
      const lastC = s.lastIndexOf(",");
      const lastD = s.lastIndexOf(".");
      if (lastC > lastD) {
        return s.replace(/\./g, "").replace(",", ".");
      }
      return s.replace(/,/g, "");
    }
    if (hasComma && !hasDot) {
      const parts = s.split(",");
      if (parts.length === 2 && parts[1].length <= 2) {
        return parts[0].replace(/\./g, "") + "." + parts[1];
      }
      return s.replace(/,/g, "");
    }
    if (hasDot && !hasComma) {
      const parts = s.split(".");
      if (parts.length === 2 && parts[1].length <= 2) {
        return parts[0].replace(/,/g, "") + "." + parts[1];
      }
      if (parts.length > 2) {
        return s.replace(/\./g, "");
      }
    }
    return s;
  };

  const jetReadRawUnitPriceStringFromPage = () => {
    let el = document.querySelector(".current-price-value");
    if (el) {
      const content = el.getAttribute("content");
      if (content != null && content !== "") {
        return content;
      }
    }

    el = document.querySelector('[itemprop="price"]');
    if (el) {
      const content = el.getAttribute("content");
      if (content != null && content !== "") {
        return content;
      }
    }

    const selectors = [
      ".product__current-price",
      ".product__price",
      ".product-price",
      ".js-product-price",
      ".current-price",
    ];
    for (const selector of selectors) {
      const node = document.querySelector(selector);
      if (!node) {
        continue;
      }
      const extracted = jetExtractNumericPriceStringFromDisplayText(
        node.textContent || "",
      );
      if (extracted !== "") {
        return extracted;
      }
    }

    const details = document.querySelector(
      "#product-details[data-product], .js-product-details[data-product]",
    );
    if (details && details.dataset && details.dataset.product) {
      try {
        const data = JSON.parse(details.dataset.product);
        if (
          data &&
          Object.prototype.hasOwnProperty.call(data, "price_amount")
        ) {
          return String(data.price_amount);
        }
        if (data && Object.prototype.hasOwnProperty.call(data, "price")) {
          return String(data.price);
        }
      } catch (e) {
        // ignore malformed JSON
      }
    }

    return "";
  };

  const jetLoad = () => {
    console.log("jetLoad");
    jetRelinkProductDom();
    if ($("#btn_jet").length) {
      if (!jetBindingsInitialized) {
        jetBindingsInitialized = true;

        $(document)
          .off("click", "#btn_jet")
          .on("click", "#btn_jet", (event) => {
            queueJetRelinkProductDom();
            card = 0;
            jetCalculate();
            jetResetPopupSteps();
            jetShowActivePopup();
          });

        $(document)
          .off("click", "#btn_jet_card")
          .on("click", "#btn_jet_card", (event) => {
            queueJetRelinkProductDom();
            card = 1;
            jetCalculate();
            jetResetPopupSteps();
            jetShowActivePopup();
          });

        $(document)
          .off("click", "#btn_preizcisli")
          .on("click", "#btn_preizcisli", (event) => {
            jetCalculate();
          });

        $(document)
          .off("change", "#jet_vnoski")
          .on("change", "#jet_vnoski", () => {
            jetCalculate();
          });

        $(document)
          .off("click", "#back_jetcredit")
          .on("click", "#back_jetcredit", (event) => {
            jetClose();
          });

        $(document)
          .off("click", "#close_jetcredit")
          .on("click", "#close_jetcredit", (event) => {
            jetClose();
          });

        $(document)
          .off("click", "#buy_jetcredit")
          .on("click", "#buy_jetcredit", (event) => {
            let _taxa = parseFloat($("#jet_vnoska_popup").text());
            if (card == 1 && $("#jet_vnoska_popup").length) {
              _taxa = parseFloat($("#jet_vnoska_popup").text());
            }
            if (_taxa >= 20) {
              $("#jet_step_1").hide("slow");
              $("#jet_step_2").show("slow");
            } else {
              $("#jet_alert_overlay").addClass("show");
              jetShowCustomAlert(
                "Месечната вноска трябва да надхвърля сумата от 20 лв.!",
                false,
              );
            }
          });

        $(document)
          .off("click", "#buy_cart_jetcredit")
          .on("click", "#buy_cart_jetcredit", (event) => {
            const jet_buy_buttons_submit = $(
              'button[data-button-action="add-to-cart"]',
            );
            if (jet_buy_buttons_submit.length) {
              jet_buy_buttons_submit.eq(0).click();
            }
            jetClose();
          });

        $(document)
          .off("click", "#back2_jetcredit")
          .on("click", "#back2_jetcredit", (event) => {
            $("#jet_step_2").hide("slow");
            $("#jet_step_1").show("slow");
          });

        $(document)
          .off("input", "#jet_egn")
          .on("input", "#jet_egn", (event) => {
            const input = event.target.value;
            const regex = /^[0-9]{0,10}$/;
            if (!regex.test(input)) {
              event.target.value = input.slice(0, -1);
            }
          });

        $(document)
          .off("input", "#jet_phone")
          .on("input", "#jet_phone", (event) => {
            const input = event.target.value;
            const regex = /^[+0-9]+$/;
            if (!regex.test(input)) {
              event.target.value = input.slice(0, -1);
            }
          });

        $(document)
          .off("click", "#buy2_jetcredit")
          .on("click", "#buy2_jetcredit", (event) => {
            if (checkForm()) {
              jetSend();
            }
          });

        $(document)
          .off("click", ".jet_input_text_active")
          .on("click", ".jet_input_text_active", (event) => {
            if (event.target.classList.contains("error")) {
              event.target.classList.remove("error");
            }
          });

        $(document)
          .off("click", "#uslovia")
          .on("click", "#uslovia", () => {
            changeBtnJetcredit();
          });

        $(document)
          .off("click", "#uslovia1")
          .on("click", "#uslovia1", () => {
            changeBtnJetcredit();
          });

        $(document)
          .off("click", "#uslovia2")
          .on("click", "#uslovia2", () => {
            changeBtnJetcreditBuy();
          });
      }

      jetResetPopupSteps();

      jetCalculate();
    }
  };

  const jetCalculate = () => {
    let jet_price1 = $("#jet_price").val();
    let jet_quantity = 1;
    if ($("#quantity_wanted").length > 0) {
      jet_quantity = parseFloat($("#quantity_wanted").val());
    }
    const rawUnitPrice = jetReadRawUnitPriceStringFromPage();
    if (rawUnitPrice !== "") {
      jet_price1 = rawUnitPrice;
    }

    const normalizedPrice =
      String(jet_price1).indexOf(",") !== -1 &&
      String(jet_price1).indexOf(".") === -1
        ? String(jet_price1).replace(",", ".")
        : String(jet_price1);
    const unitPrice = parseFloat(normalizedPrice);
    if (Number.isNaN(unitPrice)) {
      return;
    }
    const jetPriceall = unitPrice * jet_quantity;

    jetHideOptions401();
    jetHideOptions601();
    if (jetPriceall >= 401) {
      jetShowOptions401();
    }
    if (jetPriceall >= 601) {
      jetShowOptions601();
    }

    if (
      $("#jet_parva").val().trim() == "" ||
      parseFloat($("#jet_parva").val()) < jetPriceall
    ) {
      $.ajax({
        url:
          prestashop.urls.base_url +
          "index.php?fc=module&module=creditjet&controller=JetCalculate&jet_token=" +
          $("#jet_static_token").val(),
        type: "POST",
        dataType: "json",
        data: {
          jet_priceall: jetPriceall,
          jet_parva: parseFloat($("#jet_parva").val()).toFixed(2),
          jet_vnoski: parseInt($("#jet_vnoski").val()),
          jet_product_id: parseInt($("#jet_product_id").val()),
        },
        success: function (json) {
          if (json.success === "success") {
            if (json.jet_show_button) {
              $("#jet-product-button-container").show();
            } else {
              $("#jet-product-button-container").hide();
            }
            $("#jet_vnoska").text(json.jet_vnoska);
            $("#jet_vnoska_second").text(json.jet_vnoska_second);
            $("#jet_vnoski_text").text(parseInt($("#jet_vnoski").val()));
            $("#jet_priceall").text(json.jet_priceall);
            $("#jet_priceall_second").text(json.jet_priceall_second);
            $("#jet_total_credit_price").text(json.jet_total_credit_price);
            $("#jet_total_credit_price_second").text(
              json.jet_total_credit_price_second,
            );
            $("#jet_vnoska_popup").text(json.jet_vnoska);
            $("#jet_vnoska_popup_second").text(json.jet_vnoska_second);
            $("#jet_gpr").text(json.jet_gpr);
            $("#jet_glp").text(json.jet_glp);
            $("#jet_obshto").text(json.jet_obshto);
            $("#jet_obshto_second").text(json.jet_obshto_second);

            if (parseInt($("#jet_card_in").val()) === 1) {
              $("#jet_vnoska_card").text(json.jet_vnoska_card);
              $("#jet_vnoska_card_second").text(json.jet_vnoska_card_second);
              $("#jet_vnoski_text_card").text(parseInt($("#jet_vnoski").val()));
              if (card === 1) {
                $("#jet_vnoska_popup").text(json.jet_vnoska_card);
                $("#jet_vnoska_popup_second").text(json.jet_vnoska_card_second);
                $("#jet_gpr").text(json.jet_gpr_card);
                $("#jet_glp").text(json.jet_glp_card);
                $("#jet_obshto").text(json.jet_obshto_card);
                $("#jet_obshto_second").text(json.jet_obshto_card_second);
              }
            }
          }
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    } else {
      $("#jet_alert_overlay").addClass("show");
      jetShowCustomAlert(
        "Първоначалната вноска трябва да бъде по-малка от цената на стоките!",
        false,
      );
    }
  };

  const jetClose = () => {
    jetGetActivePopup().hide();
    $("#jet_step_1").show();
    $("#jet_step_2").hide();
    $("#uslovia").prop("checked", false);
    $("#uslovia1").prop("checked", false);
    $("#uslovia2").prop("checked", false);

    const jet_parva = $("#jet_parva");
    if (parseFloat(jet_parva.val()) !== 0) {
      jet_parva.val(0);
      jetCalculate();
    }
    changeBtnJetcredit();
  };

  const jetHideOptions401 = () => {
    $("#jet_vnoski")
      .find("option")
      .each(function () {
        if (
          $(this).val() === "15" ||
          $(this).val() === "18" ||
          $(this).val() === "24"
        ) {
          $(this).prop("disabled", true);
        }
      });
  };

  const jetShowOptions401 = () => {
    $("#jet_vnoski")
      .find("option")
      .each(function () {
        if (
          $(this).val() === "15" ||
          $(this).val() === "18" ||
          $(this).val() === "24"
        ) {
          $(this).prop("disabled", false);
        }
      });
  };

  const jetHideOptions601 = () => {
    $("#jet_vnoski")
      .find("option")
      .each(function () {
        if ($(this).val() === "30" || $(this).val() === "36") {
          $(this).prop("disabled", true);
        }
      });
  };

  const jetShowOptions601 = () => {
    $("#jet_vnoski")
      .find("option")
      .each(function () {
        if ($(this).val() === "30" || $(this).val() === "36") {
          $(this).prop("disabled", false);
        }
      });
  };

  const changeBtnJetcredit = () => {
    if ($("#uslovia").is(":checked") && $("#uslovia1").is(":checked")) {
      let _taxa = parseFloat($("#jet_vnoska_popup").text());
      if (card == 1 && $("#jet_vnoska_popup").length) {
        _taxa = parseFloat($("#jet_vnoska_popup").text());
      }
      if (_taxa >= 20) {
        $("#buy_jetcredit").prop("disabled", false);
        $("#buy_jetcredit").css({
          opacity: 1.0,
        });
      } else {
        $("#jet_alert_overlay").addClass("show");
        jetShowCustomAlert(
          "Месечната вноска трябва да надхвърля сумата от 20 лв.!",
          false,
        );
        $("#uslovia").prop("checked", false);
        $("#uslovia1").prop("checked", false);
      }
    } else {
      $("#buy_jetcredit").prop("disabled", true);
      $("#buy_jetcredit").css({
        opacity: 0.5,
      });
    }
  };

  const changeBtnJetcreditBuy = () => {
    if ($("#uslovia2").is(":checked")) {
      $("#buy2_jetcredit").prop("disabled", false);
      $("#buy2_jetcredit").css({
        opacity: 1.0,
      });
    } else {
      $("#buy2_jetcredit").prop("disabled", true);
      $("#buy2_jetcredit").css({
        opacity: 0.5,
      });
    }
  };

  const jetShowCustomAlert = (message, exit) => {
    const jetAlertBox = $("<div></div>", {
      id: "jet_alert_box",
      css: {
        position: "fixed",
        top: "50%",
        left: "50%",
        transform: "translate(-50%, -50%)",
        backgroundColor: "#fff",
        padding: "20px",
        borderRadius: "5px",
        boxShadow: "0 0 10px rgba(0, 0, 0, 0.1)",
        zIndex: "5000001",
        width: "300px",
        textAlign: "center",
      },
    });
    const jetMessageText = $("<p></p>", {
      text: message,
      css: {
        fontFamily: '"Roboto Condensed", sans-serif',
        color: "#14532d",
      },
    });
    jetAlertBox.append(jetMessageText);
    const jetCloseButton = $("<button></button>", {
      text: "Добре",
      css: {
        fontFamily: '"Roboto Condensed", sans-serif',
        fontWeight: "500",
        marginTop: "20px",
        padding: "10px 20px",
        border: "none",
        backgroundColor: "#166534",
        color: "#fff",
        borderRadius: "3px",
        cursor: "pointer",
      },
    });
    jetCloseButton.on("click", function () {
      jetAlertBox.remove();
      if (exit) {
        jetClose();
      }
      $("#jet_alert_overlay").removeClass("show");
    });
    jetAlertBox.append(jetCloseButton);
    $("body").append(jetAlertBox);
  };

  const checkForm = () => {
    let check = true;
    const jet_name = document.getElementById("jet_name").value.trim();
    if (jet_name === "") {
      document.getElementById("jet_name").classList.add("error");
      check = false;
    }
    const jet_lastname = document.getElementById("jet_lastname").value.trim();
    if (jet_lastname === "") {
      document.getElementById("jet_lastname").classList.add("error");
      check = false;
    }
    const egnRe =
      /^[0-9]{2}((0[1-9]|1[0-2])|(4[1-9]|5[0-2]))(0[0-9]|1[0-9]|2[0-9]|3[0-1])[0-9]{4}$/;
    const jet_egn = document.getElementById("jet_egn").value.trim();
    if (jet_egn === "" || !egnRe.test(jet_egn)) {
      document.getElementById("jet_egn").classList.add("error");
      check = false;
    }
    const phoneRe = /^[+0-9]+$/;
    const jet_phone = document.getElementById("jet_phone").value.trim();
    if (jet_phone === "" || jet_phone.length < 10 || !phoneRe.test(jet_phone)) {
      document.getElementById("jet_phone").classList.add("error");
      check = false;
    }
    const re =
      /^[a-zA-Z0-9.!#$%&'*+/=?^_'{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    const jet_email = document
      .getElementById("jet_email")
      .value.trim()
      .toLowerCase();
    const position = jet_email.indexOf("@") + 1;
    const ostatak = jet_email.substr(position);
    if (
      jet_email === "" ||
      !re.test(jet_email) ||
      ostatak.indexOf(".") === -1
    ) {
      document.getElementById("jet_email").classList.add("error");
      check = false;
    }
    return check;
  };

  const jetSend = () => {
    $("#jet_alert_overlay").addClass("show");
    let jet_quantity = 1;
    if ($("#quantity_wanted").length > 0) {
      jet_quantity = parseFloat($("#quantity_wanted").val());
    }
    var jetProductDetailsDiv = $("#product-details");
    var jetDataProductString = jetProductDetailsDiv.attr("data-product");
    var jetDataProductObject = JSON.parse(jetDataProductString);
    $.ajax({
      url:
        prestashop.urls.base_url +
        "index.php?fc=module&module=creditjet&controller=JetSend&jet_token=" +
        $("#jet_static_token").val(),
      type: "POST",
      dataType: "json",
      data: {
        jet_priceall: $("#jet_priceall").text(),
        jet_vnoski: $("#jet_vnoski").val(),
        jet_vnoska: $("#jet_vnoska_popup").text(),
        jet_parva: $("#jet_parva").val(),
        jet_total_credit_price: $("#jet_total_credit_price").text(),
        jet_obshto: $("#jet_obshto").text(),
        jet_gpr: $("#jet_gpr").text(),
        jet_glp: $("#jet_glp").text(),
        jet_name: $("#jet_name").val(),
        jet_lastname: $("#jet_lastname").val(),
        jet_egn: $("#jet_egn").val(),
        jet_email: $("#jet_email").val(),
        jet_phone: $("#jet_phone").val(),
        jet_lname: $("#jet_lname").val(),
        jet_card: card,
        jet_product_id: $("#jet_product_id").val(),
        jet_quantity: jet_quantity,
        jet_product_attribute_id: jetDataProductObject.id_product_attribute,
      },
      success: function (json) {
        if (json.success === "success") {
          jetShowCustomAlert(
            "Успешно изпратихте Вашата заявка за лизинг към ПБ Лични Финанси. Очаквайте контакт за потвърждаване на направената от Вас заявка.",
            true,
          );
        } else {
          jetShowCustomAlert(
            "Не можете да изпратите Вашата заявка за лизинг към ПБ Лични Финанси. Моля опитайте по-късно.",
            false,
          );
        }
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  };

  const jetContainsDivWithClass = (nodeList, className) => {
    for (var i = 0; i < nodeList.length; i++) {
      var node = nodeList[i];
      if (node.tagName === "DIV" && node.classList.contains(className)) {
        return true;
      }
    }
    return false;
  };

  $(document).on(
    "change input",
    "#quantity_wanted, input[name='qty'], input[name=qty]",
    function () {
      scheduleJetProductRefresh();
    },
  );

  // Variation/combination changes for themes that do not always emit updatedProduct* reliably.
  $(document).on(
    "change",
    [
      "#group_1",
      "#group_2",
      "#group_3",
      "select[name^='group']",
      "input[type='radio'][name^='group']",
      ".input-color",
      ".js-product-variants select",
      ".js-product-variants input",
      "#product-variants select",
      "#product-variants input",
    ].join(", "),
    function () {
      scheduleJetProductRefreshBurst();
    },
  );

  // Click-based variant UIs (swatches/buttons) used by some themes.
  $(document).on(
    "click",
    [
      ".js-product-variants [data-product-attribute]",
      ".js-product-variants .input-color",
      "#product-variants [data-product-attribute]",
      "#product-variants .input-color",
      ".product-variants [data-product-attribute]",
      ".product-variants .input-color",
    ].join(", "),
    function () {
      scheduleJetProductRefreshBurst();
    },
  );

  if (
    typeof prestashop !== "undefined" &&
    typeof prestashop.on === "function"
  ) {
    prestashop.on("updatedProduct", function () {
      scheduleJetProductRefreshBurst();
    });
    prestashop.on("updatedProductCombination", function () {
      scheduleJetProductRefreshBurst();
    });
  }

  // Some themes dispatch these as jQuery document events instead of prestashop.on callbacks.
  $(document).on("updatedProduct updatedProductCombination", function () {
    scheduleJetProductRefreshBurst();
  });

  jetLoad();
  scheduleJetProductRefresh();
  setTimeout(function () {
    if (document.getElementById("jet_price")) {
      scheduleJetProductRefresh();
    }
  }, 350);
});
