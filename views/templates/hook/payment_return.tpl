{*
* CreditJet Payment Module
* Payment return template
*}

<p>{l s='Вашата поръчка е завършена успешно.' mod='creditjet'}</p>

<div class="box">
    <h3>{l s='Информация за поръчката' mod='creditjet'}</h3>
    <ul>
        <li><strong>{l s='Номер на поръчка:' mod='creditjet'}</strong> {$id_order}</li>
        <li><strong>{l s='Обща сума:' mod='creditjet'}</strong> {$total_to_pay}</li>
        <li><strong>{l s='Метод на плащане:' mod='creditjet'}</strong>
            {l s='CreditJet - Париба лични финанси' mod='creditjet'}</li>
    </ul>
</div>

<p>{l s='Ще получите имейл с потвърждение на поръчката.' mod='creditjet'}</p>
<p>{l s='Благодарим за покупката!' mod='creditjet'}</p>