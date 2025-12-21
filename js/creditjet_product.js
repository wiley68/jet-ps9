jQuery(document).ready(function ($) {
    let card
    card = 0

    const jetLoad = () => {
        if ($('#btn_jet').length) {
            $('#jet_step_1').show()
            $('#jet_step_2').hide()

            $('#btn_jet').on('click', (event) => {
                card = 0
                jetCalculate()
                $('#jet-product-popup-container').show()
            })

            if ($('#btn_jet_card').length) {
                $('#btn_jet_card').on('click', (event) => {
                    card = 1
                    jetCalculate()
                    $('#jet-product-popup-container').show()
                })
            }

            $('#btn_preizcisli').on('click', (event) => {
                jetCalculate()
            })

            $('#jet_vnoski').on('change', (event) => {
                jetCalculate()
            })

            $('#back_jetcredit').on('click', (event) => {
                jetClose()
            })

            $('#close_jetcredit').on('click', (event) => {
                jetClose()
            })

            $('#buy_jetcredit').on('click', (event) => {
                let _taxa = parseFloat($('#jet_vnoska_popup').text())
                if (card == 1 && $('#jet_vnoska_popup').length) {
                    _taxa = parseFloat($('#jet_vnoska_popup').text())
                }
                if (_taxa >= 20) {
                    $('#jet_step_1').hide('slow')
                    $('#jet_step_2').show('slow')
                } else {
                    $('#jet_alert_overlay').addClass('show')
                    jetShowCustomAlert(
                        'Месечната вноска трябва да надхвърля сумата от 20 лв.!',
                        false
                    )
                }
            })

            $('#buy_cart_jetcredit').on('click', (event) => {
                const jet_buy_buttons_submit = $(
                    'button[data-button-action="add-to-cart"]'
                )
                if (jet_buy_buttons_submit.length) {
                    jet_buy_buttons_submit.eq(0).click()
                }
                jetClose()
            })

            $('#back2_jetcredit').on('click', (event) => {
                $('#jet_step_2').hide('slow')
                $('#jet_step_1').show('slow')
            })

            $('#jet_egn').on('input', (event) => {
                const input = event.target.value
                const regex = /^[0-9]{0,10}$/
                if (!regex.test(input)) {
                    event.target.value = input.slice(0, -1)
                }
            })

            $('#jet_phone').on('input', (event) => {
                const input = event.target.value
                const regex = /^[+0-9]+$/
                if (!regex.test(input)) {
                    event.target.value = input.slice(0, -1)
                }
            })

            $('#buy2_jetcredit').on('click', (event) => {
                if (checkForm()) {
                    jetSend()
                }
            })

            $('.jet_input_text_active').on('click', (event) => {
                if (event.target.classList.contains('error')) {
                    event.target.classList.remove('error')
                }
            })

            $('#uslovia').on('click', () => {
                changeBtnJetcredit()
            })

            $('#uslovia1').on('click', () => {
                changeBtnJetcredit()
            })

            $('#uslovia2').on('click', () => {
                changeBtnJetcreditBuy()
            })

            jetCalculate()
        }
    }

    const jetCalculate = () => {
        let jet_price1 = $('#jet_price').val()
        let jet_quantity = 1
        if ($('#quantity_wanted').length > 0) {
            jet_quantity = parseFloat($('#quantity_wanted').val())
        }
        // get variation price
        let jet_price_content = $('span.current-price-value')
        if (jet_price_content.length > 0) {
            jet_price1 = jet_price_content.attr('content')
        } else {
            jet_price_content = $('span.price[itemprop="price"]')
            if (jet_price_content.length > 0) {
                jet_price1 = jet_price_content.attr('content')
            }
        }

        const jetPriceall = parseFloat(jet_price1) * jet_quantity

        jetHideOptions401()
        jetHideOptions601()
        if (jetPriceall >= 401) {
            jetShowOptions401()
        }
        if (jetPriceall >= 601) {
            jetShowOptions601()
        }

        if (
            $('#jet_parva').val().trim() == '' ||
            parseFloat($('#jet_parva').val()) < jetPriceall
        ) {
            $.ajax({
                url:
                    prestashop.urls.base_url +
                    'index.php?fc=module&module=creditjet&controller=JetCalculate&jet_token=' + $('#jet_static_token').val(),
                type: 'POST',
                dataType: 'json',
                data: {
                    jet_priceall: jetPriceall,
                    jet_parva: parseFloat($('#jet_parva').val()).toFixed(2),
                    jet_vnoski: parseInt($('#jet_vnoski').val()),
                    jet_product_id: parseInt($('#jet_product_id').val()),
                },
                success: function (json) {
                    if (json.success === 'success') {
                        if (json.jet_show_button) {
                            $('#jet-product-button-container').show()
                        } else {
                            $('#jet-product-button-container').hide()
                        }
                        $('#jet_vnoska').text(json.jet_vnoska)
                        $('#jet_vnoska_second').text(json.jet_vnoska_second)
                        $('#jet_vnoski_text').text(parseInt($('#jet_vnoski').val()))
                        $('#jet_priceall').text(json.jet_priceall)
                        $('#jet_priceall_second').text(json.jet_priceall_second)
                        $('#jet_total_credit_price').text(json.jet_total_credit_price)
                        $('#jet_total_credit_price_second').text(
                            json.jet_total_credit_price_second
                        )
                        $('#jet_vnoska_popup').text(json.jet_vnoska)
                        $('#jet_vnoska_popup_second').text(json.jet_vnoska_second)
                        $('#jet_gpr').text(json.jet_gpr)
                        $('#jet_glp').text(json.jet_glp)
                        $('#jet_obshto').text(json.jet_obshto)
                        $('#jet_obshto_second').text(json.jet_obshto_second)

                        if (parseInt($('#jet_card_in').val()) === 1) {
                            $('#jet_vnoska_card').text(json.jet_vnoska_card)
                            $('#jet_vnoska_card_second').text(json.jet_vnoska_card_second)
                            $('#jet_vnoski_text_card').text(parseInt($('#jet_vnoski').val()))
                            if (card === 1) {
                                $('#jet_vnoska_popup').text(json.jet_vnoska_card)
                                $('#jet_vnoska_popup_second').text(json.jet_vnoska_card_second)
                                $('#jet_gpr').text(json.jet_gpr_card)
                                $('#jet_glp').text(json.jet_glp_card)
                                $('#jet_obshto').text(json.jet_obshto_card)
                                $('#jet_obshto_second').text(json.jet_obshto_card_second)
                            }
                        }
                    }
                },
                error: function (error) {
                    console.error('Error:', error)
                },
            })
        } else {
            $('#jet_alert_overlay').addClass('show')
            jetShowCustomAlert(
                'Първоначалната вноска трябва да бъде по-малка от цената на стоките!',
                false
            )
        }
    }

    const jetClose = () => {
        $('#jet-product-popup-container').hide()
        $('#jet_step_1').show()
        $('#jet_step_2').hide()
        $('#uslovia').prop('checked', false)
        $('#uslovia1').prop('checked', false)
        $('#uslovia2').prop('checked', false)

        const jet_parva = $('#jet_parva')
        if (parseFloat(jet_parva.val()) !== 0) {
            jet_parva.val(0)
            jetCalculate()
        }
        changeBtnJetcredit()
    }

    const jetHideOptions401 = () => {
        $('#jet_vnoski')
            .find('option')
            .each(function () {
                if (
                    $(this).val() === '15' ||
                    $(this).val() === '18' ||
                    $(this).val() === '24'
                ) {
                    $(this).prop('disabled', true)
                }
            })
    }

    const jetShowOptions401 = () => {
        $('#jet_vnoski')
            .find('option')
            .each(function () {
                if (
                    $(this).val() === '15' ||
                    $(this).val() === '18' ||
                    $(this).val() === '24'
                ) {
                    $(this).prop('disabled', false)
                }
            })
    }

    const jetHideOptions601 = () => {
        $('#jet_vnoski')
            .find('option')
            .each(function () {
                if ($(this).val() === '30' || $(this).val() === '36') {
                    $(this).prop('disabled', true)
                }
            })
    }

    const jetShowOptions601 = () => {
        $('#jet_vnoski')
            .find('option')
            .each(function () {
                if ($(this).val() === '30' || $(this).val() === '36') {
                    $(this).prop('disabled', false)
                }
            })
    }

    const changeBtnJetcredit = () => {
        if ($('#uslovia').is(':checked') && $('#uslovia1').is(':checked')) {
            let _taxa = parseFloat($('#jet_vnoska_popup').text())
            if (card == 1 && $('#jet_vnoska_popup').length) {
                _taxa = parseFloat($('#jet_vnoska_popup').text())
            }
            if (_taxa >= 20) {
                $('#buy_jetcredit').prop('disabled', false)
                $('#buy_jetcredit').css({
                    opacity: 1.0,
                })
            } else {
                $('#jet_alert_overlay').addClass('show')
                jetShowCustomAlert(
                    'Месечната вноска трябва да надхвърля сумата от 20 лв.!',
                    false
                )
                $('#uslovia').prop('checked', false)
                $('#uslovia1').prop('checked', false)
            }
        } else {
            $('#buy_jetcredit').prop('disabled', true)
            $('#buy_jetcredit').css({
                opacity: 0.5,
            })
        }
    }

    const changeBtnJetcreditBuy = () => {
        if ($('#uslovia2').is(':checked')) {
            $('#buy2_jetcredit').prop('disabled', false)
            $('#buy2_jetcredit').css({
                opacity: 1.0,
            })
        } else {
            $('#buy2_jetcredit').prop('disabled', true)
            $('#buy2_jetcredit').css({
                opacity: 0.5,
            })
        }
    }

    const jetShowCustomAlert = (message, exit) => {
        const jetAlertBox = $('<div></div>', {
            id: 'jet_alert_box',
            css: {
                position: 'fixed',
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%)',
                backgroundColor: '#fff',
                padding: '20px',
                borderRadius: '5px',
                boxShadow: '0 0 10px rgba(0, 0, 0, 0.1)',
                zIndex: '5000001',
                width: '300px',
                textAlign: 'center',
            },
        })
        const jetMessageText = $('<p></p>', {
            text: message,
            css: {
                fontFamily: '"Roboto Condensed", sans-serif',
                color: '#14532d',
            },
        })
        jetAlertBox.append(jetMessageText)
        const jetCloseButton = $('<button></button>', {
            text: 'Добре',
            css: {
                fontFamily: '"Roboto Condensed", sans-serif',
                fontWeight: '500',
                marginTop: '20px',
                padding: '10px 20px',
                border: 'none',
                backgroundColor: '#166534',
                color: '#fff',
                borderRadius: '3px',
                cursor: 'pointer',
            },
        })
        jetCloseButton.on('click', function () {
            jetAlertBox.remove()
            if (exit) {
                jetClose()
            }
            $('#jet_alert_overlay').removeClass('show')
        })
        jetAlertBox.append(jetCloseButton)
        $('body').append(jetAlertBox)
    }

    const checkForm = () => {
        let check = true
        const jet_name = document.getElementById('jet_name').value.trim()
        if (jet_name === '') {
            document.getElementById('jet_name').classList.add('error')
            check = false
        }
        const jet_lastname = document.getElementById('jet_lastname').value.trim()
        if (jet_lastname === '') {
            document.getElementById('jet_lastname').classList.add('error')
            check = false
        }
        const egnRe =
            /^[0-9]{2}((0[1-9]|1[0-2])|(4[1-9]|5[0-2]))(0[0-9]|1[0-9]|2[0-9]|3[0-1])[0-9]{4}$/
        const jet_egn = document.getElementById('jet_egn').value.trim()
        if (jet_egn === '' || !egnRe.test(jet_egn)) {
            document.getElementById('jet_egn').classList.add('error')
            check = false
        }
        const phoneRe = /^[+0-9]+$/
        const jet_phone = document.getElementById('jet_phone').value.trim()
        if (jet_phone === '' || jet_phone.length < 10 || !phoneRe.test(jet_phone)) {
            document.getElementById('jet_phone').classList.add('error')
            check = false
        }
        const re =
            /^[a-zA-Z0-9.!#$%&'*+/=?^_'{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
        const jet_email = document
            .getElementById('jet_email')
            .value.trim()
            .toLowerCase()
        const position = jet_email.indexOf('@') + 1
        const ostatak = jet_email.substr(position)
        if (
            jet_email === '' ||
            !re.test(jet_email) ||
            ostatak.indexOf('.') === -1
        ) {
            document.getElementById('jet_email').classList.add('error')
            check = false
        }
        return check
    }

    const jetSend = () => {
        $('#jet_alert_overlay').addClass('show')
        let jet_quantity = 1
        if ($('#quantity_wanted').length > 0) {
            jet_quantity = parseFloat($('#quantity_wanted').val())
        }
        var jetProductDetailsDiv = $('#product-details')
        var jetDataProductString = jetProductDetailsDiv.attr('data-product')
        var jetDataProductObject = JSON.parse(jetDataProductString)
        $.ajax({
            url:
                prestashop.urls.base_url +
                'index.php?fc=module&module=creditjet&controller=JetSend&jet_token=' + $('#jet_static_token').val(),
            type: 'POST',
            dataType: 'json',
            data: {
                jet_priceall: $('#jet_priceall').text(),
                jet_vnoski: $('#jet_vnoski').val(),
                jet_vnoska: $('#jet_vnoska_popup').text(),
                jet_parva: $('#jet_parva').val(),
                jet_total_credit_price: $('#jet_total_credit_price').text(),
                jet_obshto: $('#jet_obshto').text(),
                jet_gpr: $('#jet_gpr').text(),
                jet_glp: $('#jet_glp').text(),
                jet_name: $('#jet_name').val(),
                jet_lastname: $('#jet_lastname').val(),
                jet_egn: $('#jet_egn').val(),
                jet_email: $('#jet_email').val(),
                jet_phone: $('#jet_phone').val(),
                jet_lname: $('#jet_lname').val(),
                jet_card: card,
                jet_product_id: $('#jet_product_id').val(),
                jet_quantity: jet_quantity,
                jet_product_attribute_id: jetDataProductObject.id_product_attribute,
            },
            success: function (json) {
                if (json.success === 'success') {
                    jetShowCustomAlert(
                        'Успешно изпратихте Вашата заявка за лизинг към ПБ Лични Финанси. Очаквайте контакт за потвърждаване на направената от Вас заявка.',
                        true
                    )
                } else {
                    jetShowCustomAlert(
                        'Не можете да изпратите Вашата заявка за лизинг към ПБ Лични Финанси. Моля опитайте по-късно.',
                        false
                    )
                }
            },
            error: function (error) {
                console.error('Error:', error)
            },
        })
    }

    const jetContainsDivWithClass = (nodeList, className) => {
        for (var i = 0; i < nodeList.length; i++) {
            var node = nodeList[i]
            if (node.tagName === 'DIV' && node.classList.contains(className)) {
                return true
            }
        }
        return false
    }

    /* body */
    const targetNode = document.querySelector('div.product-actions')
    if (targetNode !== null) {
        if (targetNode instanceof Node) {
            const observer = new MutationObserver(mutationCallback)
            const config = {
                childList: true,
                subtree: true,
            }
            function mutationCallback(mutationsList, observer) {
                for (let mutation of mutationsList) {
                    if (!mutation.target.id.startsWith('jet_')) {
                        if (mutation.target.tagName == 'FORM') {
                            if (
                                jetContainsDivWithClass(mutation.addedNodes, 'product-variants')
                            ) {
                                jetLoad()
                            }
                        }
                    }
                }
            }
            observer.observe(targetNode, config)
        }
    }

    jetLoad()
})
