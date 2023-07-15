<?php
if (!defined("ABSPATH")) exit; 

//  Register shortcode
add_shortcode( 'seren_loan_calculator', 'seren_loan_calculator_callback' );

//  Handle callback
function seren_loan_calculator_callback( $atts ) {
    // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

    $prix = esc_html( array_key_exists('price', $atts) ? $atts['price'] : '100000' );
    $fraisNotaire = esc_html(get_option('frais_notaire_neuf'));
    $apport = esc_html(get_option('apport_recommande'));
    $taux = esc_html(get_option('taux_par_default'));
    $duree = esc_html(get_option('duree_par_default'));

    ob_start();
    ?>
        <section id="seren-loan-calculator">
            <h2 class="title">Combien me coûtera <b>ce bien chaque mois ?</b></h2>
            <table class="form-table">
                <tr>
                    <th>
                        <label>Prix du bien</label>
                        <input class="field" id="serenloan-price" type="number" value="-1" autocomplete="off">
                    </th>
                    <th>
                        <label>Frais de notaire (<?php echo $fraisNotaire ?>% dans le neuf)</label>
                        <input class="field" id="serenloan-notaryfees" type="number" value="-1" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th>
                        <label>Apport (<?php echo $apport ?>% recommandé)</label>
                        <input class="field" id="serenloan-contrib" type="number" value="-1" autocomplete="off">
                    </th>
                    <th>
                        <label>Durée du prêt</label>
                        <input class="field" id="serenloan-duration" type="number" value="-1" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th>
                        <label>Taux (en %, assurance incluse)</label>
                        <input class="field" id="serenloan-rate" type="number" value="-1" autocomplete="off">
                    </th>
                </tr>
            </table>
            <div class="result">
                <label class="monthly">0</label>
            </div>
        </section>



        <script>
            document.addEventListener("DOMContentLoaded", function(){
                const settings = {
                    notaryFees: +"<?php echo $fraisNotaire ?>",
                    contribution: +"<?php echo $apport ?>",
                    rate: +"<?php echo $taux ?>",
                    duration: +"<?php echo $duree ?>",
                    price: +"<?php echo $prix ?>",
                };
                new SerenLoanCalculator(settings);
            });

            class SerenLoanCalculator {
                constructor(initialSettings) {
                    this.elems = {
                        // Inputs
                        price: document.getElementById("serenloan-price"),
                        notaryFees: document.getElementById("serenloan-notaryfees"),
                        contrib: document.getElementById("serenloan-contrib"),
                        duration: document.getElementById("serenloan-duration"),
                        rate: document.getElementById("serenloan-rate"),
                        // Results
                        resultMonthly: document.querySelector("#seren-loan-calculator .result .monthly"),
                    };
                    this.initialSettings = initialSettings;
                    this.init()
                }

                init() {
                    this.elems.price.setAttribute("value", this.initialSettings.price);
                    this.elems.contrib.setAttribute("value", this.calculateP100(this.initialSettings.price, this.initialSettings.contribution));
                    this.elems.notaryFees.setAttribute("value", this.calculateP100(this.initialSettings.price, this.initialSettings.notaryFees));
                    this.elems.duration.setAttribute("value", this.initialSettings.duration);
                    this.elems.rate.setAttribute("value", this.initialSettings.rate);
                    //  Attach event listener on all inputs
                    for (const [key, input] of Object.entries(this.elems)) {
                        if(input.className !== "field") continue;
                        input.addEventListener("input", (ev) => {
                            input.setAttribute("value", ev.target.value);
                            this.calculate();
                        });
                    }
                    this.calculate();
                }

                calculateP100 = (baseValue, pourcent) => Math.round(baseValue * pourcent / 100, 2);

                formatNumber(x) {
                    var parts = x.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                    return parts.join(".");
                }

                calculate() {
                    const price = +this.elems.price.getAttribute("value");
                    const notaryFees = +this.elems.notaryFees.getAttribute("value");
                    const contribution = +this.elems.contrib.getAttribute("value");
                    const duration = +this.elems.duration.getAttribute("value");
                    const rate = +this.elems.rate.getAttribute("value");

                    const C = price + notaryFees - contribution;
                    const t = rate / 12 / 100;
                    const m = (C * t) / (1 - Math.pow(1 + t, -(duration * 12)));

                    let formattedResult = "Données invalides";
                    if(!isNaN(m)) {
                        formattedResult = this.formatNumber(parseFloat(m.toFixed(2)));
                    }
                    this.elems.resultMonthly.innerHTML = formattedResult;
                }
            }

        </script>
    <?php
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}


