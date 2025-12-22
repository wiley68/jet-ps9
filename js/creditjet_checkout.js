jQuery(document).ready(function ($) {
  const debounce = (func, delay) => {
    let timeoutId;
    return function (...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        func.apply(this, args);
      }, delay);
    };
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
            if ($("#jet_vnoska").length !== 0) {
              $("#jet_vnoska").text(json.jet_vnoska);
              $("#jet_vnoska_input").val(json.jet_vnoska);
              if ($("#jet_vnoska_second").length !== 0)
                $("#jet_vnoska_second").text(json.jet_vnoska_second);
              $("#jet_priceall").text(json.jet_priceall);
              $("#jet_priceall_input").val(json.jet_priceall);
              if ($("#jet_priceall_second").length !== 0)
                $("#jet_priceall_second").text(json.jet_priceall_second);
              $("#jet_total_credit_price").text(json.jet_total_credit_price);
              if ($("#jet_total_credit_price_second").length !== 0)
                $("#jet_total_credit_price_second").text(
                  json.jet_total_credit_price_second
                );
              $("#jet_gpr").text(json.jet_gpr);
              $("#jet_glp").text(json.jet_glp);
              $("#jet_obshto").text(json.jet_obshto);
              if ($("#jet_obshto_second").length !== 0)
                $("#jet_obshto_second").text(json.jet_obshto_second);
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
        0
      );
    }
  };

  const jetCalculateCard = () => {
    const jetPriceall = parseFloat($("#jet_card_price").val());
    jetHideOptions401();
    jetHideOptions601();
    if (jetPriceall >= 401) {
      jetShowOptions401();
    }
    if (jetPriceall >= 601) {
      jetShowOptions601();
    }

    if (
      $("#jet_card_parva").val().trim() == "" ||
      parseFloat($("#jet_card_parva").val()) < jetPriceall
    ) {
      $.ajax({
        url:
          prestashop.urls.base_url +
          "index.php?fc=module&module=creditjet&controller=JetCalculate&jet_token=" +
          $("#jet_static_token_card").val(),
        type: "POST",
        dataType: "json",
        data: {
          jet_priceall: jetPriceall.toFixed(2),
          jet_parva: parseFloat($("#jet_card_parva").val()).toFixed(2),
          jet_vnoski: parseInt($("#jet_card_vnoski").val()),
          jet_product_id: $("#jet_card_products_id").val(),
        },
        success: function (json) {
          if (json.success === "success") {
            if ($("#jet_card_vnoska").length !== 0) {
              $("#jet_card_vnoska").text(json.jet_vnoska_card);
              $("#jet_card_vnoska_input").val(json.jet_vnoska_card);
              if ($("#jet_card_vnoska_second").length !== 0)
                $("#jet_card_vnoska_second").text(json.jet_vnoska_card_second);
              $("#jet_card_priceall").text(json.jet_priceall);
              $("#jet_card_priceall_input").val(json.jet_priceall);
              if ($("#jet_card_priceall_second").length !== 0)
                $("#jet_card_priceall_second").text(json.jet_priceall_second);
              $("#jet_card_total_credit_price").text(
                json.jet_total_credit_price
              );
              if ($("#jet_card_total_credit_price_second").length !== 0)
                $("#jet_card_total_credit_price_second").text(
                  json.jet_total_credit_price_second
                );
              $("#jet_card_gpr").text(json.jet_gpr);
              $("#jet_card_glp").text(json.jet_glp);
              $("#jet_card_obshto").text(json.jet_obshto_card);
              if ($("#jet_card_obshto_second").length !== 0)
                $("#jet_card_obshto_second").text(json.jet_obshto_card_second);
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
        1
      );
    }
  };

  const changeBtnJetcredit = () => {
    if (
      $("#jet_uslovia").is(":checked") &&
      $("#jet_uslovia1").is(":checked") &&
      $("#jet_egn").val() != ""
    ) {
      let _taxa = parseFloat($("#jet_vnoska").text());
      if (_taxa >= 20) {
        $("#jet_btn_pay").prop("disabled", false);
        $("#jet_btn_pay").css({
          opacity: 1.0,
        });
      } else {
        $("#jet_alert_overlay").addClass("show");
        jetShowCustomAlert(
          "Месечната вноска трябва да надхвърля сумата от 20 лв.!",
          false,
          0
        );
        $("#jet_uslovia").prop("checked", false);
        $("#jet_uslovia1").prop("checked", false);
      }
    } else {
      $("#jet_btn_pay").prop("disabled", true);
      $("#jet_btn_pay").css({
        opacity: 0.5,
      });
    }
  };

  const changeBtnJetcreditCard = () => {
    if (
      $("#jet_card_uslovia").is(":checked") &&
      $("#jet_card_uslovia1").is(":checked") &&
      $("#jet_card_egn").val() != ""
    ) {
      let _taxa = parseFloat($("#jet_card_vnoska").text());
      if (_taxa >= 20) {
        $("#jet_card_btn_pay").prop("disabled", false);
        $("#jet_card_btn_pay").css({
          opacity: 1.0,
        });
      } else {
        $("#jet_card_alert_overlay").addClass("show");
        jetShowCustomAlert(
          "Месечната вноска трябва да надхвърля сумата от 20 лв.!",
          false,
          1
        );
        $("#jet_card_uslovia").prop("checked", false);
        $("#jet_card_uslovia1").prop("checked", false);
      }
    } else {
      $("#jet_card_btn_pay").prop("disabled", true);
      $("#jet_card_btn_pay").css({
        opacity: 0.5,
      });
    }
  };

  const jetShowCustomAlert = (message, exit, card) => {
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
      if (card == 0) {
        $("#jet_alert_overlay").removeClass("show");
      } else {
        $("#jet_card_alert_overlay").removeClass("show");
      }
    });
    jetAlertBox.append(jetCloseButton);
    $("body").append(jetAlertBox);
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

  function handleJetEgnChange(event) {
    if (event.target.id == "jet_egn") {
      const input = event.target.value;
      const regex = /^[0-9]{0,10}$/;
      if (!regex.test(input)) {
        event.target.value = input.slice(0, -1);
      }
      jetCalculate();
      changeBtnJetcredit();
    }
    if (event.target.id == "jet_card_egn") {
      const input = event.target.value;
      const regex = /^[0-9]{0,10}$/;
      if (!regex.test(input)) {
        event.target.value = input.slice(0, -1);
      }
      jetCalculateCard();
      changeBtnJetcreditCard();
    }
  }

  document.addEventListener("input", debounce(handleJetEgnChange, 1000));

  document.addEventListener("blur", (event) => {
    if (event.target.id == "jet_egn") {
      jetCalculate();
    }
    if (event.target.id == "jet_card_egn") {
      jetCalculateCard();
    }
  });

  document.addEventListener("click", (event) => {
    if (event.target.id == "btn_preizcisli") {
      jetCalculate();
    }
    if (event.target.id == "jet_card_btn_preizcisli") {
      jetCalculateCard();
    }
    if (event.target.id == "jet_uslovia" || event.target.id == "jet_uslovia1") {
      jetCalculate();
      changeBtnJetcredit();
    }
    if (
      event.target.id == "jet_card_uslovia" ||
      event.target.id == "jet_card_uslovia1"
    ) {
      jetCalculateCard();
      changeBtnJetcreditCard();
    }
  });

  document.addEventListener("change", (event) => {
    if (event.target.id == "jet_vnoski") {
      jetCalculate();
    }
    if (event.target.id == "jet_card_vnoski") {
      jetCalculateCard();
    }
  });

  $(document.body).on(
    "click",
    'input[type="radio"][name="payment-option"]',
    function () {
      if ($(this).attr("data-module-name") == "creditjet") {
        $("form#conditions-to-approve").hide();
        $("div#payment-confirmation").removeClass("js-payment-confirmation");
        $("div#payment-confirmation").hide();
        jetCalculate();
      } else {
        if ($(this).attr("data-module-name") == "creditjetcard") {
          $("form#conditions-to-approve").hide();
          $("div#payment-confirmation").removeClass("js-payment-confirmation");
          $("div#payment-confirmation").hide();
          jetCalculateCard();
        } else {
          console.log($(this).attr("data-module-name"));
          $("form#conditions-to-approve").show();
          $("div#payment-confirmation").addClass("js-payment-confirmation");
          $("div#payment-confirmation").show();
        }
      }
    }
  );
});
