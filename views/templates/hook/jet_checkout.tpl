<form action="{$jet_action}" method="post">
    <input type="hidden" id="jet_price" name="jet_price" value="{$jet_price}" />
    <input type="hidden" id="jet_products_id" name="jet_products_id" value="{$jet_products_id}" />
    <input type="hidden" id="jet_products_qt" name="jet_products_qt" value="{$jet_products_qt}" />
    <input type="hidden" id="jet_products_pr" name="jet_products_pr" value="{$jet_products_pr}" />
    <input type="hidden" id="jet_products_ct" name="jet_products_ct" value="{$jet_products_ct}" />
    <input type="hidden" id="jet_products_vr" name="jet_products_vr" value="{$jet_products_vr}" />
    <input type="hidden" id="jet_static_token" value="{$static_token}" />

    <div id="jet_alert_overlay" class="jet_alert_overlay"></div>
    <div class="jet_panel">
        <div class="jet_row">
            <div class="{$jet_column_left}">
                Първоначална вноска ({$jet_sign})
            </div>
            <div class="{$jet_column_right}">
                <input class="jet_input_text_active" type="number" min="0" id="jet_parva" name="jet_parva_input"
                    value=0 />
                <button type="button" id="btn_preizcisli" class="jet_button_preizcisli">Преизчисли</button>
            </div>
        </div>
        <div class="jet_row">
            <div class="{$jet_column_left}">
                {if $jet_sign_second == ''}
                    Цена на стоките ({$jet_sign})
                {else}
                    Цена на стоките ({$jet_sign}
                    <span style='font-size:70%;font-weight:400;height:14px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                {/if}
            </div>
            <div class="jet_column_right">
                <input type="hidden" id="jet_priceall_input" name="jet_priceall_input" />
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
                <select id="jet_vnoski" name="jet_vnoski_input" class="jet_input_text">
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
                    Общо кредит ({$jet_sign})
                {else}
                    Общо кредит ({$jet_sign}
                    <span style='font-size:70%;font-weight:400;height:14px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                {/if}
            </div>
            <div class="jet_column_right">
                <input type="hidden" id="jet_total_credit_price_input" name="jet_total_credit_price_input" />
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
                    <span style='font-size:70%;font-weight:400;height:14px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                {/if}
            </div>
            <div class="jet_column_right">
                <input type="hidden" id="jet_vnoska_input" name="jet_vnoska_input" />
                {if $jet_eur == 0 or $jet_eur == 3}
                    <div class="jet_input_text jet_disable">
                        <div><span id="jet_vnoska"></span></div>
                        <div></div>
                    </div>
                {else}
                    <div class="jet_input_text jet_disable">
                        <div><span id="jet_vnoska"></span></div>
                        <div>
                            <span>/</span><span id="jet_vnoska_second"></span>
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
                <input type="hidden" id="jet_gpr_input" name="jet_gpr_input" />
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
                <input type="hidden" id="jet_glp_input" name="jet_glp_input" />
                <div class="jet_input_text jet_disable">
                    <div><span id="jet_glp"></span></div>
                    <div></div>
                </div>
            </div>
        </div>
        <div class="jet_row">
            <div class="{$jet_column_left}">
                {if $jet_sign_second == ''}
                    Общо плащания ({$jet_sign})
                {else}
                    Общо плащания ({$jet_sign}
                    <span style='font-size:70%;font-weight:400;height:14px;'>&nbsp;/&nbsp;{$jet_sign_second}</span>)
                {/if}
            </div>
            <div class="jet_column_right">
                <input type="hidden" id="jet_obshto_input" name="jet_obshto_input" />
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
        <div class="jet_row">
            <div class="{$jet_column_left}">
                ЕГН *
            </div>
            <div class="jet_column_right">
                <input class="jet_input_text_active jet_left" type="text" id="jet_egn" name="jet_egn" maxlength="10" />
            </div>
        </div>
        <div class="jet_hr"></div>
        <div class="jet_row_footer">
            <div style="padding-bottom: 5px;">
                <input type="checkbox" name="jet_uslovia" value="1" id="jet_uslovia" class="jet_uslovia" />
                &nbsp;&nbsp;&nbsp;
                <a href="https://www.postbank.bg/common-conditions-PFBG" class="jet_uslovia_a"
                    title="Условия за кандидатстване на ПБ Лични Финанси" target="_blank">
                    <span style="font-size: 12px;">Запознах се с условията за кандидатстване на ПБ Лични финанси</span>
                </a>
            </div>
            <div>
                <input type="checkbox" name="jet_uslovia1" value="1" id="jet_uslovia1" class="jet_uslovia" />
                &nbsp;&nbsp;&nbsp;
                <a href="https://www.postbank.bg/Personal-Data-PFBG-retailers" class="jet_uslovia_a"
                    title="Регламент (ЕС) 2016/679 от 27 април 2016 г. за защита на физическите лица по отношение на обработката на лични данни и за свободното движение на такива данни и за отмяна на Директива 95/46 / ЕО"
                    target="_blank">
                    <span style="font-size: 12px;">"GDPR" означава Регламент (ЕС) 2016/679 от 27 април 2016 г. за защита
                        на физическите лица по отношение на обработката на лични данни и за свободното движение на
                        такива данни и за отмяна на Директива 95/46 / ЕО</span>
                </a>
            </div>
            <button type="submit" id="jet_btn_pay" disabled class="btn btn-primary">Изпрати заявката</button>
        </div>
    </div>
</form>