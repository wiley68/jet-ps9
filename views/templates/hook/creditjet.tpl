<div id="jet-product-button-container" class="creditjet-product-button-scope" style="padding-top:{$jet_gap}px;">
    <input type="hidden" id="jet_price" value="{$jet_price}" />
    <input type="hidden" id="jet_product_id" value="{$jet_id_product}" />
    <input type="hidden" id="jet_card_in" value="{$jet_card_in}" />
    <input type="hidden" id="jet_static_token" value="{$static_token}" />

    <div id="jet_alert_overlay" class="jet_alert_overlay"></div>
    <div id="jet_alert_box"></div>
    {if $zaiavka1jet_text != ''}
        <table class="jet_table">
            <tr>
                <td class="jet_button_table">
                    <div class="jet_button_div_txt">
                        {$zaiavka1jet_text}
                    </div>
                </td>
            </tr>
        </table>
    {/if}
    <table class="jet_table_img">
        <tr>
            <td class="jet_button_table">
                <img id="btn_jet" class="jet_logo" src="{$modules_dir}creditjet/views/templates/img/jet.png"
                    alt="Кредитен модул ПБ Лични Финанси" title="Кредитен модул ПБ Лични Финанси" />
            </td>
        </tr>
        {if $is_vnoska == 1}
            <tr>
                <td class="jet_button_table">
                    {if $jet_sign_second == ''}
                        <p><span id="jet_vnoski_text"></span> x <span id="jet_vnoska"></span> {$jet_sign}</p>
                    {else}
                        <p>
                            <span id="jet_vnoski_text"></span> x <span id="jet_vnoska"></span> {$jet_sign}<br />
                            <span style="font-size:75%;font-weight:400;">(<span id="jet_vnoska_second"></span>
                                {$jet_sign_second})</span>
                        </p>
                    {/if}
                </td>
            </tr>
        {/if}
    </table>
    {if $jet_card_in == 1}
        <table class="jet_table_img">
            <tr>
                <td class="jet_button_table">
                    <img id="btn_jet_card" class="jet_logo" src="{$modules_dir}creditjet/views/templates/img/jet_card.png"
                        alt="Специални предложения само за клиенти, които вече имат кредитна карта, издадена от ПБ Лични Финанси."
                        title="Специални предложения само за клиенти, които вече имат кредитна карта, издадена от ПБ Лични Финанси." />
                </td>
            </tr>
            {if $is_vnoska == 1}
                <tr>
                    <td class="jet_button_table">
                        {if $jet_sign_second == ''}
                            <p><span id="jet_vnoski_text_card"></span> x <span id="jet_vnoska_card"></span> {$jet_sign}</p>
                        {else}
                            <p>
                                <span id="jet_vnoski_text_card"></span> x <span id="jet_vnoska_card"></span> {$jet_sign}<br />
                                <span style="font-size:75%;font-weight:400;">(<span id="jet_vnoska_card_second"></span>
                                    {$jet_sign_second})</span>
                            </p>
                        {/if}
                    </td>
                </tr>
            {/if}
        </table>
    {/if}
</div>

<div id="jet-product-popup-container" class="{$modalpayment_jet} creditjet-product-popup-scope">
    <div class="modalpayment-content_jet">
        <div id="jet_body">
            <div class="{$jet_PopUp_Detailed_v1}">
                <div class="{$jet_Mask}">

                    <div id="jet_step_1">
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                Първоначална вноска ({$jet_sign})
                            </div>
                            <div class="{$jet_column_right}">
                                <input class="jet_input_text_active" type="number" min="0" id="jet_parva" value=0 />
                                <button type="button" id="btn_preizcisli"
                                    class="jet_button_preizcisli">Преизчисли</button>
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                {if $jet_sign_second == ''}
                                    Цена на стоките ({$jet_sign})
                                {else}
                                    Цена на стоките ({$jet_sign}
                                    <span
                                        style='font-size:60%;font-weight:400;height:16px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                                {/if}
                            </div>
                            <div class="jet_column_right">
                                {if $jet_eur == 0 or $jet_eur == 3}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_priceall"></span></div>
                                        <div></div>
                                    </div>
                                {else}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_priceall"></span></div>
                                        <div>
                                            <span>/</span><span id="jet_priceall_second"></span>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                Брой погасителни вноски
                            </div>
                            <div class="jet_column_right">
                                <select id="jet_vnoski" class="jet_input_text">
                                    <option value="3" {if $jet_vnoski == 3}selected{/if}>3 месеца</option>
                                    <option value="6" {if $jet_vnoski == 6}selected{/if}>6 месеца</option>
                                    <option value="9" {if $jet_vnoski == 9}selected{/if}>9 месеца</option>
                                    <option value="10" {if $jet_vnoski == 10}selected{/if}>10 месеца</option>
                                    <option value="12" {if $jet_vnoski == 12}selected{/if}>12 месеца</option>
                                    <option value="15" {if $jet_vnoski == 15}selected{/if}>15 месеца</option>
                                    <option value="18" {if $jet_vnoski == 18}selected{/if}>18 месеца</option>
                                    <option value="24" {if $jet_vnoski == 24}selected{/if}>24 месеца</option>
                                    <option value="30" {if $jet_vnoski == 30}selected{/if}>30 месеца</option>
                                    <option value="36" {if $jet_vnoski == 36}selected{/if}>36 месеца</option>
                                </select>
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                {if $jet_sign_second == ''}
                                    {$obshto_credit_text} ({$jet_sign})
                                {else}
                                    {$obshto_credit_text} ({$jet_sign}
                                    <span
                                        style='font-size:60%;font-weight:400;height:16px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                                {/if}
                            </div>
                            <div class="jet_column_right">
                                {if $jet_eur == 0 or $jet_eur == 3}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_total_credit_price"></span></div>
                                        <div></div>
                                    </div>
                                {else}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_total_credit_price"></span></div>
                                        <div>
                                            <span>/</span><span id="jet_total_credit_price_second"></span>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                {if $jet_sign_second == ''}
                                    Месечна вноска ({$jet_sign})
                                {else}
                                    Месечна вноска ({$jet_sign}
                                    <span
                                        style='font-size:60%;font-weight:400;height:16px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                                {/if}
                            </div>
                            <div class="jet_column_right">
                                {if $jet_eur == 0 or $jet_eur == 3}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_vnoska_popup"></span></div>
                                        <div></div>
                                    </div>
                                {else}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_vnoska_popup"></span></div>
                                        <div>
                                            <span>/</span><span id="jet_vnoska_popup_second"></span>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                Фикс ГПР (%)
                            </div>
                            <div class="jet_column_right">
                                <div class="jet_input_text jet_disable">
                                    <div><span id="jet_gpr"></span></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                ГЛП (%)
                            </div>
                            <div class="jet_column_right">
                                <div class="jet_input_text jet_disable">
                                    <div><span id="jet_glp"></span></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                {if $jet_sign_second == ''}
                                    {$obshto_plashtania} ({$jet_sign})
                                {else}
                                    {$obshto_plashtania} ({$jet_sign}
                                    <span
                                        style='font-size:60%;font-weight:400;height:16px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                                {/if}
                            </div>
                            <div class="jet_column_right">
                                {if $jet_eur == 0 or $jet_eur == 3}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_obshto"></span></div>
                                        <div></div>
                                    </div>
                                {else}
                                    <div class="jet_input_text jet_disable">
                                        <div><span id="jet_obshto"></span></div>
                                        <div>
                                            <span>/</span><span id="jet_obshto_second"></span>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="jet_hr"></div>
                        <div class="jet_row_footer">
                            <div style="padding-bottom: 5px;">
                                <input type="checkbox" name="uslovia" value="uslovia" id="uslovia"
                                    class="jet_uslovia" />
                                &nbsp;&nbsp;&nbsp;
                                <a href="https://www.postbank.bg/common-conditions-PFBG" class="jet_uslovia_a"
                                    title="Условия за кандидатстване на ПБ Лични Финанси" target="_blank">
                                    <span style="font-size: 14px;">Запознах се с условията за кандидатстване на ПБ Лични
                                        финанси</span>
                                </a>
                            </div>
                            <div>
                                <input type="checkbox" name="uslovia1" value="uslovia1" id="uslovia1"
                                    class="jet_uslovia" />
                                &nbsp;&nbsp;&nbsp;
                                <a href="https://www.postbank.bg/Personal-Data-PFBG-retailers" class="jet_uslovia_a"
                                    title="Регламент (ЕС) 2016/679 от 27 април 2016 г. за защита на физическите лица по отношение на обработката на лични данни и за свободното движение на такива данни и за отмяна на Директива 95/46 / ЕО"
                                    target="_blank">
                                    <span style="font-size: 14px;">"GDPR" означава Регламент (ЕС) 2016/679 от 27 април
                                        2016 г. за защита на физическите лица по отношение на обработката на лични данни
                                        и за свободното движение на такива данни и за отмяна на Директива 95/46 /
                                        ЕО</span>
                                </a>
                            </div>
                        </div>
                        <div class="jet_row_bottom">
                            <button type="button" class="jet_btn" id="back_jetcredit">Откажи</button>
                            <button type="button" class="jet_btn" id="buy_cart_jetcredit">Добави в количката</button>
                            <button type="button" class="jet_btn" id="buy_jetcredit" style="opacity: 0.5;" disabled>Купи
                                на изплащане</button>
                        </div>
                    </div>

                    <div id="jet_step_2">
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                Име *
                            </div>
                            <div class="jet_column_right">
                                <input class="jet_input_text_active jet_left" type="text" id="jet_name"
                                    autocomplete="off" value="{$jet_name}" />
                                <input type="hidden" id="jet_lname" autocomplete="off" value="" />
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                Фамилия *
                            </div>
                            <div class="jet_column_right">
                                <input class="jet_input_text_active jet_left" type="text" id="jet_lastname"
                                    autocomplete="off" value="{$jet_lastname}" />
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                ЕГН *
                            </div>
                            <div class="jet_column_right">
                                <input class="jet_input_text_active jet_left" type="text" id="jet_egn"
                                    autocomplete="off" value="" />
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                Мобилен телефон *
                            </div>
                            <div class="jet_column_right">
                                <input class="jet_input_text_active jet_left" type="text" id="jet_phone"
                                    autocomplete="off" value="{$jet_phone}" />
                            </div>
                        </div>
                        <div class="jet_row">
                            <div class="{$jet_column_left}">
                                E-Mail *
                            </div>
                            <div class="jet_column_right">
                                <input class="jet_input_text_active jet_left" type="text" id="jet_email"
                                    autocomplete="off" value="{$jet_email}" />
                            </div>
                        </div>
                        <div class="jet_hr"></div>
                        <div class="jet_row_footer">
                            <div style="padding-bottom: 5px;">
                                <input type="checkbox" name="uslovia2" value="uslovia2" id="uslovia2"
                                    class="jet_uslovia" />
                                &nbsp;&nbsp;&nbsp;
                                <a href="https://www.postbank.bg/common-conditions-PFBG" class="jet_uslovia_a"
                                    title="Условия за кандидатстване на ПБ Лични Финанси" target="_blank">
                                    <span style="font-size: 14px;">Запознах се с условията за кандидатстване на ПБ Лични
                                        финанси</span>
                                </a>
                            </div>
                            <div style="padding-bottom: 5px;">
                                <a href="https://www.postbank.bg/product-information-PBPG-retailers"
                                    class="jet_uslovia_a" title="Продуктова Информация на ПБ Лични финанси"
                                    target="_blank">
                                    <span style="font-size: 14px;">Продуктова Информация на ПБ Лични финанси</span>
                                </a>
                            </div>
                        </div>
                        <div class="jet_row_bottom">
                            <button type="button" class="jet_btn" id="back2_jetcredit">Назад</button>
                            <div style="flex: 1;"></div>
                            <button type="button" class="jet_btn" id="close_jetcredit">Откажи</button>
                            <button type="button" class="jet_btn" id="buy2_jetcredit" style="opacity: 0.5;"
                                disabled>Изпрати</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>