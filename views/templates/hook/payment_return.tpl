{*
* CreditJet Payment Module
* Payment return template
*}

<p>{l s='Your order has been completed successfully.' mod='creditjet'}</p>

<div class="box">
    <h3>{l s='Order Information' mod='creditjet'}</h3>
    <ul>
        <li><strong>{l s='Order Number:' mod='creditjet'}</strong> {$id_order}</li>
        <li><strong>{l s='Total Amount:' mod='creditjet'}</strong> {$total_to_pay}</li>
        <li><strong>{l s='Payment Method:' mod='creditjet'}</strong>
            {l s='CreditJet - Piraeus Personal Finance' mod='creditjet'}</li>
    </ul>
</div>

<p>{l s='You will receive an email confirmation of your order.' mod='creditjet'}</p>
<p>{l s='Thank you for your purchase!' mod='creditjet'}</p>