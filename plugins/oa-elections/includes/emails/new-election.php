<?php
$fname        = current( $fields['_oa_election_leader_fname'] );
$unit_num     = current( $fields['_oa_election_unit_number'] );
$unit_type    = current( $fields['_oa_election_unit_type'] );
$date_1       = current( $fields['_oa_election_unit_date_1'] );
$date_2       = current( $fields['_oa_election_unit_date_2'] );
$date_3       = current( $fields['_oa_election_unit_date_3'] );
$leader_email = current( $fields['_oa_election_leader_email'] );
?>

Hi <?php echo esc_html( $fname ); ?>,<br />

Please save this email for your records. Thanks for requesting an OA election for <?php echo esc_html( $unit_type ); ?>
<?php echo esc_html( $unit_num ); ?>! Here are the dates you requested:<br /><br />

<?php echo esc_html( $date_1 ); ?><br />
<?php echo esc_html( $date_2 ); ?><br />
<?php echo esc_html( $date_3 ); ?><br />

We’ll notify you automatically when your chapter officers confirm a date. To save everyone time be sure to add eligible Scouts to your election. You can log into your election request using these credentials:<br /><br />

Username: <?php echo esc_html( $leader_email ); ?><br />
Password: unit#+firstletterofchapter+lastname+383<br /><br />

You have what you need for now. In case you have questions you can contact elections@tahosalodge.org and we’ll respond as quickly as we can.<br /><br />

In Scouting,<br /><br />

Unit Elections Committee<br />
Tahosa Lodge 383
