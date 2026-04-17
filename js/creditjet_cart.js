jQuery(document).ready(function ($) {
  let card;
  card = 0;
  const JET_EUR_BGN_RATE = 1.95583;
  let jetCartRefreshObserver = null;
  let jetCartRefreshTimer = null;
  let jetObservedCartRoot = null;
  let jetCartRecalcBusy = false;
  let jetCartNetworkHooksReady = false;

  /* function */
  const jetLoad = () => {
    if ($("#btn_jet").length !== 0) {
      $("#jet_step_1").css("display", "block");
      $("#jet_step_2").css("display", "none");

      $("#btn_jet").on("click", function (event) {
        card = 0;
        jetCalculate();
        $("#jet-product-popup-container").css("display", "block");
      });

      if ($("#btn_jet_card").length !== 0) {
        $("#btn_jet_card").on("click", function (event) {
          card = 1;
          jetCalculate();
          $("#jet-product-popup-container").css("display", "block");
        });
      }

      $("#back_jetcredit").on("click", function (event) {
        jetClose();
      });

      $("#close_jetcredit").on("click", function (event) {
        jetClose();
      });

      $("#buy_jetcredit").on("click", function (event) {
        let _taxa = parseFloat($("#jet_vnoska_popup").text());
        if (card == 1 && $("#jet_vnoska_popup").length !== 0) {
          _taxa = parseFloat($("#jet_vnoska_popup").text());
        }
        if (_taxa >= 20) {
          $("#jet_step_1").css("display", "none");
          $("#jet_step_2").css("display", "block");
        } else {
          $("#jet_alert_overlay").addClass("show");
          jetShowCustomAlert(
            "Месечната вноска трябва да надхвърля сумата от 20 лв.!",
            false,
          );
        }
      });

      $("#back2_jetcredit").on("click", function (event) {
        $("#jet_step_2").css("display", "none");
        $("#jet_step_1").css("display", "block");
      });

      $("#jet_egn").on("input", function (event) {
        const input = $(this).val();
        const regex = /^[0-9]{0,10}$/;
        if (!regex.test(input)) {
          $(this).val(input.slice(0, -1));
        }
      });

      $("#jet_phone").on("input", function (event) {
        const input = $(this).val();
        const regex = /^[+0-9]+$/;
        if (!regex.test(input)) {
          $(this).val(input.slice(0, -1));
        }
      });

      $("#buy2_jetcredit").on("click", function (event) {
        if (checkForm()) {
          jetSend();
        }
      });

      $(".jet_input_text_active").on("click", function (event) {
        if ($(this).hasClass("error")) {
          $(this).removeClass("error");
        }
      });

      jetCalculate();
    }
  };

  const jetCalculate = () => {
    const jetPriceall = parseFloat($("#jet_price").val());
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
          jet_priceall: jetPriceall.toFixed(2),
          jet_parva: parseFloat($("#jet_parva").val()).toFixed(2),
          jet_vnoski: parseInt($("#jet_vnoski").val()),
          jet_product_id: $("#jet_products_id").val(),
        },
        success: function (json) {
          if (json.success === "success") {
            if (json.jet_show_button) {
              $("#jet-product-button-container").show();
            } else {
              $("#jet-product-button-container").hide();
            }
            if ($("#jet_vnoska").length !== 0) {
              $("#jet_vnoska").text(json.jet_vnoska);
              if ($("#jet_vnoska_second").length !== 0)
                $("#jet_vnoska_second").text(json.jet_vnoska_second);
              $("#jet_vnoski_text").text(parseInt($("#jet_vnoski").val()));
            }
            $("#jet_priceall").text(json.jet_priceall);
            if ($("#jet_priceall_second").length !== 0)
              $("#jet_priceall_second").text(json.jet_priceall_second);
            $("#jet_total_credit_price").text(json.jet_total_credit_price);
            if ($("#jet_total_credit_price_second").length !== 0)
              $("#jet_total_credit_price_second").text(
                json.jet_total_credit_price_second,
              );
            $("#jet_vnoska_popup").text(json.jet_vnoska);
            if ($("#jet_vnoska_popup_second").length !== 0)
              $("#jet_vnoska_popup_second").text(json.jet_vnoska_second);
            $("#jet_gpr").text(json.jet_gpr);
            $("#jet_glp").text(json.jet_glp);
            $("#jet_obshto").text(json.jet_obshto);
            if ($("#jet_obshto_second").length !== 0)
              $("#jet_obshto_second").text(json.jet_obshto_second);

            if (parseInt($("#jet_card_in").val()) == 1) {
              if ($("#jet_vnoska_card").length !== 0) {
                $("#jet_vnoska_card").text(json.jet_vnoska_card);
                if ($("#jet_vnoska_card_second").length !== 0)
                  $("#jet_vnoska_card_second").text(
                    json.jet_vnoska_card_second,
                  );
                $("#jet_vnoski_text_card").text(
                  parseInt($("#jet_vnoski").val()),
                );
              }
              if (card == 1) {
                $("#jet_vnoska_popup").text(json.jet_vnoska_card);
                if ($("#jet_vnoska_popup_second").length !== 0)
                  $("#jet_vnoska_popup_second").text(
                    json.jet_vnoska_card_second,
                  );
                $("#jet_gpr").text(json.jet_gpr_card);
                $("#jet_glp").text(json.jet_glp_card);
                $("#jet_obshto").text(json.jet_obshto_card);
                if ($("#jet_obshto_second").length !== 0)
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
    $("#jet-product-popup-container").css("display", "none");
    $("#jet_step_1").css("display", "block");
    $("#jet_step_2").css("display", "none");
    $("#uslovia").prop("checked", false);
    $("#uslovia1").prop("checked", false);
    $("#uslovia2").prop("checked", false);
    const $jet_parva = $("#jet_parva");
    if (parseFloat($("#jet_parva").val()) !== 0) {
      $("#jet_parva").val(0);
      jetCalculate();
    }
    changeBtnJetcredit();
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
        window.location.href = window.location.origin;
      }
      $("#jet_alert_overlay").removeClass("show");
    });
    jetAlertBox.append(jetCloseButton);
    $("body").append(jetAlertBox);
  };

  const jetSend = () => {
    $("#jet_alert_overlay").addClass("show");
    $.ajax({
      url:
        prestashop.urls.base_url +
        "index.php?fc=module&module=creditjet&controller=JetSendCart&jet_token=" +
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
        jet_products_id: $("#jet_products_id").val(),
        jet_products_qt: $("#jet_products_qt").val(),
        jet_products_pr: $("#jet_products_pr").val(),
        jet_products_ct: $("#jet_products_ct").val(),
        jet_products_vr: $("#jet_products_vr").val(),
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
      if (card == 1 && $("#jet_vnoska_popup").length !== 0) {
        _taxa = parseFloat($("#jet_vnoska_popup").text());
      }
      if (_taxa >= 20) {
        $("#buy_jetcredit").prop("disabled", false).css({
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
      $("#buy_jetcredit").prop("disabled", true).css({
        opacity: 0.5,
      });
    }
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

  const jetDebounce = (func, delay) => {
    let inDebounce;
    return function () {
      const context = this;
      const args = arguments;
      clearTimeout(inDebounce);
      inDebounce = setTimeout(() => func.apply(context, args), delay);
    };
  };

  const jetRelinkCartPopup = () => {
    const $all = $("#jet-product-popup-container");
    if ($all.length === 0) {
      return;
    }
    const $host = $(".cart-grid, #wrapper, #content-wrapper, body").first();
    let $keep = $all.last();
    if ($host.length) {
      const $inside = $all.filter(function () {
        return $.contains($host[0], this);
      });
      if ($inside.length) {
        $keep = $inside.last();
      }
    }
    $all.not($keep).remove();
    $keep.prependTo("body");
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

  const jetReadCartTotalRawStringFromPage = () => {
    const selectors = [
      ".cart-summary__line.cart-total .cart-summary__value",
      ".cart-summary__totals .cart-summary__line.cart-total .cart-summary__value",
      ".cart-summary__line.cart-total .cart-summary__line__value .cart-summary__value",
      ".cart-summary__line.cart-total .cart-summary__line__value",
      ".cart-summary__line.cart-total .value",
      ".cart-summary-line.cart-total .value",
      ".cart-summary-totals .value",
      ".cart__totals .cart-summary-line .value",
      ".cart-total .value",
      ".product-line__discount .product-line__price strong",
      ".product-line__discount .product-line__price",
      ".cart__total .price",
      ".cart__total .value",
    ];
    for (let i = 0; i < selectors.length; i += 1) {
      const el = document.querySelector(selectors[i]);
      if (!el) {
        continue;
      }
      const fromContent = el.getAttribute("content");
      if (fromContent != null && fromContent !== "") {
        return fromContent;
      }
      const extracted = jetExtractNumericPriceStringFromDisplayText(
        el.textContent || "",
      );
      if (extracted !== "") {
        return extracted;
      }
    }

    const lineTotalNodes = document.querySelectorAll(
      ".product-line__content-right .product-line__price, .cart__item .product-line__price",
    );
    if (lineTotalNodes && lineTotalNodes.length > 0) {
      let sum = 0;
      let used = 0;
      for (let i = 0; i < lineTotalNodes.length; i += 1) {
        const rawLine = jetExtractNumericPriceStringFromDisplayText(
          lineTotalNodes[i].textContent || "",
        );
        if (rawLine === "") {
          continue;
        }
        const normalizedLine =
          rawLine.indexOf(".") !== -1 ? rawLine : rawLine.replace(/,/g, ".");
        const parsedLine = parseFloat(normalizedLine);
        if (Number.isNaN(parsedLine)) {
          continue;
        }
        sum += parsedLine;
        used += 1;
      }
      if (used > 0) {
        return sum.toFixed(2);
      }
    }

    return "";
  };

  const jetSyncCartTotalFromDom = () => {
    const jetPriceEl = document.getElementById("jet_price");
    if (!jetPriceEl) {
      return;
    }

    const raw = jetReadCartTotalRawStringFromPage();
    if (raw === "") {
      return;
    }
    const normalized = raw.indexOf(".") !== -1 ? raw : raw.replace(/,/g, ".");
    const parsed = parseFloat(normalized);
    if (Number.isNaN(parsed)) {
      return;
    }

    jetPriceEl.value = String(parsed);

    const jetPriceMain = document.getElementById("jet_priceall");
    const jetPriceSecond = document.getElementById("jet_priceall_second");
    const jetEurEl = document.getElementById("jet_eur");
    if (jetPriceMain) {
      jetPriceMain.textContent = parsed.toFixed(2);
    }
    if (jetPriceSecond && jetEurEl) {
      const jetEur = parseInt(jetEurEl.value, 10);
      if (jetEur === 1) {
        jetPriceSecond.textContent = (parsed / JET_EUR_BGN_RATE).toFixed(2);
      } else if (jetEur === 2) {
        jetPriceSecond.textContent = (parsed * JET_EUR_BGN_RATE).toFixed(2);
      }
    }
  };

  const jetScheduleCartRecalculation = () => {
    if (jetCartRecalcBusy) {
      return;
    }
    if (jetCartRefreshTimer !== null) {
      clearTimeout(jetCartRefreshTimer);
    }
    jetCartRefreshTimer = setTimeout(function () {
      jetCartRefreshTimer = null;
      jetCartRecalcBusy = true;
      jetSetupAjaxCartObserver();
      jetSyncCartTotalFromDom();
      jetRelinkCartPopup();
      jetCalculate();
      setTimeout(function () {
        jetCartRecalcBusy = false;
      }, 250);
    }, 120);
  };

  const jetScheduleCartRecalculationBurst = () => {
    jetScheduleCartRecalculation();
    setTimeout(jetScheduleCartRecalculation, 300);
    setTimeout(jetScheduleCartRecalculation, 700);
  };

  const jetSetupAjaxCartObserver = () => {
    if (typeof MutationObserver === "undefined") {
      return;
    }
    const cartRoot = document.querySelector(".cart-overview.js-cart, .js-cart");
    if (!cartRoot) {
      return;
    }
    if (jetCartRefreshObserver !== null && jetObservedCartRoot === cartRoot) {
      return;
    }
    if (jetCartRefreshObserver !== null) {
      jetCartRefreshObserver.disconnect();
    }
    jetCartRefreshObserver = new MutationObserver(function (mutations) {
      for (let i = 0; i < mutations.length; i += 1) {
        const mutation = mutations[i];
        if (
          mutation.type === "childList" &&
          (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0)
        ) {
          jetScheduleCartRecalculation();
          return;
        }
        if (
          mutation.type === "characterData" ||
          mutation.type === "attributes"
        ) {
          jetScheduleCartRecalculation();
          return;
        }
      }
    });
    jetObservedCartRoot = cartRoot;
    jetCartRefreshObserver.observe(cartRoot, {
      childList: true,
      subtree: true,
      characterData: true,
      attributes: true,
      attributeFilter: ["value", "data-refresh-url"],
    });
  };

  const jetIsCartAjaxUrl = (url) => {
    if (typeof url !== "string" || url === "") {
      return false;
    }
    return (
      url.indexOf("action=refresh") !== -1 ||
      url.indexOf("update=1") !== -1 ||
      url.indexOf("delete=1") !== -1
    );
  };

  const jetSetupCartNetworkHooks = () => {
    if (jetCartNetworkHooksReady) {
      return;
    }
    jetCartNetworkHooksReady = true;

    if (typeof window.fetch === "function") {
      const originalFetch = window.fetch;
      window.fetch = function (input, init) {
        let reqUrl = "";
        if (typeof input === "string") {
          reqUrl = input;
        } else if (input && typeof input.url === "string") {
          reqUrl = input.url;
        }
        return originalFetch.call(this, input, init).then(function (resp) {
          if (jetIsCartAjaxUrl(reqUrl)) {
            jetScheduleCartRecalculationBurst();
          }
          return resp;
        });
      };
    }

    if (
      typeof window.XMLHttpRequest === "function" &&
      window.XMLHttpRequest.prototype
    ) {
      const xhrProto = window.XMLHttpRequest.prototype;
      const originalOpen = xhrProto.open;
      const originalSend = xhrProto.send;
      xhrProto.open = function (method, url) {
        this.__jetCartUrl = typeof url === "string" ? url : "";
        return originalOpen.apply(this, arguments);
      };
      xhrProto.send = function () {
        if (
          this.__jetCartHooked !== true &&
          jetIsCartAjaxUrl(this.__jetCartUrl || "")
        ) {
          this.__jetCartHooked = true;
          this.addEventListener("loadend", function () {
            jetScheduleCartRecalculationBurst();
          });
        }
        return originalSend.apply(this, arguments);
      };
    }
  };

  const changeBtnJetcreditBuy = () => {
    if ($("#uslovia2").is(":checked")) {
      $("#buy2_jetcredit").prop("disabled", false).css({
        opacity: 1.0,
      });
    } else {
      $("#buy2_jetcredit").prop("disabled", true).css({
        opacity: 0.5,
      });
    }
  };

  /* body */
  document.addEventListener("click", (event) => {
    if (event.target.id == "uslovia" || event.target.id == "uslovia1") {
      changeBtnJetcredit();
    }
    if (event.target.id == "uslovia2") {
      changeBtnJetcreditBuy();
    }
    if (event.target.id == "btn_preizcisli") {
      jetCalculate();
    }
  });

  document.addEventListener("change", (event) => {
    if (event.target.id == "jet_vnoski") {
      jetCalculate();
    }
  });

  $("#jet-product-popup-container").prependTo("body");
  jetRelinkCartPopup();
  jetSetupAjaxCartObserver();
  jetSetupCartNetworkHooks();
  if (
    typeof window.prestashop !== "undefined" &&
    typeof window.prestashop.on === "function"
  ) {
    window.prestashop.on("updatedCart", function () {
      jetScheduleCartRecalculation();
    });
    window.prestashop.on("updateCart", function () {
      jetScheduleCartRecalculationBurst();
    });
  }
  $(document).on(
    "click",
    ".remove-from-cart, .js-remove-from-cart, [data-link-action='delete-from-cart']",
    function () {
      jetScheduleCartRecalculationBurst();
    },
  );
  $(document).on(
    "click",
    ".js-increment-button, .js-decrement-button",
    function () {
      jetScheduleCartRecalculationBurst();
    },
  );
  $(document).on("change input", ".js-cart-line-product-quantity", function () {
    jetScheduleCartRecalculationBurst();
  });

  jetLoad();
  jetScheduleCartRecalculation();
});
