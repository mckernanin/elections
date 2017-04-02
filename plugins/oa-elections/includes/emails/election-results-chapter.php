Hi <?php echo esc_html( $chapter_admin->data->user_nicename ); ?>,<br />

The election results have been entered for <?php echo esc_html( $unit_type ); ?> <?php echo esc_html( $unit_num ); ?>.<br /><br />

<?php echo esc_html( OAE_Util::elected_candidate_count( $post_id ) ); ?> candidates were elected.

<a href="<?php echo esc_attr( $post_url ); ?>">Click here</a> to see more details on this election.

<em>If you need any technical support, or something here seems wrong, please contact Kevin McKernan.</em>
