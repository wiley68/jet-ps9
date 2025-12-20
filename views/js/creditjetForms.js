const create_creditjet_schema = () => {
    if (
        $("#jet_product_id").val() !== '' ||
        $("#jet_product_meseci").val() !== '' ||
        $("#jet_product_price").val() !== '' ||
        $("#jet_product_start").val() !== '' ||
        $("#jet_product_end").val() !== ''
    ) {
        $.ajax({
            url: $("#link_to_create_schema_creditjet").val(),
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
                    alert(json.text);
                    window.location.reload();
                } else {
                    alert(json.text);
                }
            }
        });
    } else {
        alert("Имате непопълнени полета!");
    }
};

const delete_creditjet_schema = (id) => {
    $.ajax({
        url: $("#link_to_delete_schema_creditjet").val(),
        type: 'POST',
        dataType: 'json',
        data: {
            jet_product_id: id
        },
        success: function (json) {
            if (json.success == 'success') {
                alert("Успешно изтрихте филтъра.");
                window.location.reload();
            }
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
