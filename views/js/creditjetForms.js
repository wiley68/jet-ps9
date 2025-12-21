const create_creditjet_schema = () => {
    if (
        $("#jet_product_id").val() !== '' ||
        $("#jet_product_meseci").val() !== '' ||
        $("#jet_product_price").val() !== '' ||
        $("#jet_product_start").val() !== '' ||
        $("#jet_product_end").val() !== ''
    ) {
        $.ajax({
            url: window.creditjetCreateSchemaUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                jet_product_id: $("#jet_product_id").val(),
                jet_product_percent: $("#jet_product_percent option:selected").val(),
                jet_product_meseci: $("#jet_product_meseci").val(),
                jet_product_price: $("#jet_product_price").val(),
                jet_product_start: $("#jet_product_start").val(),
                jet_product_end: $("#jet_product_end").val()
            },
            success: function (json) {
                if (json.success == 'success') {
                    showSuccessMessage(json.text || window.creditjetTranslations.createSuccess);
                    window.location.reload();
                } else {
                    showErrorMessage(json.text || window.creditjetTranslations.createError);
                }
            },
            error: function (xhr) {
                console.log('AJAX error', xhr.status, xhr.responseText);
                showErrorMessage(window.creditjetTranslations.ajaxError + ': ' + xhr.status);
            }
        });
    } else {
        showErrorMessage(window.creditjetTranslations.emptyFields);
    }
};

const delete_creditjet_schema = (id) => {
    $.ajax({
        url: window.creditjetDeleteSchemaUrl,
        type: 'POST',
        dataType: 'json',
        data: {
            jet_product_id: id
        },
        success: function (json) {
            if (json.success == 'success') {
                showSuccessMessage(json.text || window.creditjetTranslations.deleteSuccess);
                window.location.reload();
            } else {
                showErrorMessage(json.text || window.creditjetTranslations.deleteError);
            }
        },
        error: function (xhr) {
            console.log('AJAX error', xhr.status, xhr.responseText);
            showErrorMessage('AJAX error: ' + xhr.status);
        }
    });
}

$(document).ready(function () {
    window.prestashop.component.initComponents([
        'ChoiceTable',
        'MultipleChoiceTable',
    ]);

    new window.prestashop.component.ChoiceTree('#form_category_choice_tree_type');
    new window.prestashop.component.ChoiceTree('#form_material_choice_tree_type');
    new window.prestashop.component.ChoiceTree(
        '#form_shop_choices_tree_type'
    ).enableAutoCheckChildren();
});
