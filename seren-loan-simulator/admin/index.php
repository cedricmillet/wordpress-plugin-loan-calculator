<?php
    if (!defined("ABSPATH")) exit;
    require_once('init.php');
?>

<div class="wrap">
    <h2>Un problème avec le plugin ?</h2>
    <p>Cette extension est écrite sur mesure pour SEREN, en cas de besoin contacter Cédric.</p>
</div>

<div class="wrap">
    <h2>Comment intégrer la calculateur dans la page souhaitée ?</h2>
    <p>Afin d'intégrer le calculateur dans la page souhaitée :</p>
    <ul>
        <li>- Ouvrez la page souhaitée avec Elementor</li>
        <li>- Insérez un bloc "Code court" dans la section désirée: <b>[seren_loan_calculator price="280000"]</b></li>
    </ul>
</div>

<div class="wrap">
    <h2>Configuration</h2>
    <form method="post" action="options.php">
        <?php
            settings_fields( 'smashing_fields' );
            do_settings_sections( 'smashing_fields' );
            submit_button();
        ?>
    </form>
</div>