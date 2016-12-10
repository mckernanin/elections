<?php
$candidates = OAE_Fields::get( 'candidates' );
$status = OAE_Util::get_status();
$disabled = null;
if ( 'Results Entered' === $status ) {
	$disabled = 'disabled';
}
?>
<table class="election-data">
	<tr>
		<th>
			Election Information
		</th>
		<th>

		</th>
	</tr>
	<tr>
		<td>
			Registered Active Youth (Under 21)
		</td>
		<td>
			<input type="number"
				name="registeredActiveYouth"
				id="registeredActiveYouth"
				value="<?php echo OAE_Fields::get('registered_youth'); ?>"
				<?php echo $disabled; ?>
			/>
		</td>
	</tr>
	<tr>
		<td>
			Youth Attendance
		</td>
		<td>
			<input type="number"
				name="youthAttendance"
				id="youthAttendance"
				value="<?php echo OAE_Fields::get('youth_attendance'); ?>"
				<?php echo $disabled; ?>
			/>
		</td>
	</tr>
</table>

<table class="election-data">
	<tr>
		<th>
			Election
		</th>
		<th>
			Ballots Turned In
		</th>
		<th>
			Votes Required
		</th>
	</tr>
	<tr>
		<td>
			Election #1
		</td>
		<td>
			<input type="number"
				name="electionOneBallots"
				id="electionOneBallots"
				value="<?php echo OAE_Fields::get('election_one_ballots'); ?>"
				<?php echo $disabled; ?>
			/>
		</td>
		<td>
			<input type="number" name="electionOneRequired" id="electionOneRequired" disabled />
		</td>
	</tr>
	<tr>
		<td>
			Election #2
		</td>
		<td>
			<input type="number"
				name="electionTwoBallots"
				id="electionTwoBallots"
				value="<?php echo OAE_Fields::get('election_two_ballots'); ?>"
				<?php echo $disabled; ?>
			/>
		</td>
		<td>
			<input type="number" name="electionTwoRequired" id="electionTwoRequired" disabled />
		</td>
	</tr>
</table>

<table>
	<tr>
		<th>
			Candidate
		</th>
		<th>
			Elected?
		</th>
	</tr>
	<?php
		foreach ( $candidates as $candidate ) {
	?>
		<tr>
			<td>
				<a href="<?php the_permalink( $candidate ); ?>"><?php echo get_the_title( $candidate ); ?></a>
			</td>
			<td>
				<?php
				if ( 'Results Entered' === $status ) {
					echo OAE_Util::get_cand_status( $candidate );
				} else { ?>
					<input type="checkbox" name="<?php echo $candidate; ?>" />
				<?php
				}
				?>
			</td>
		</tr>
	<?php } ?>
</table>
<?php
if ( 'Results Entered' !== $status ) { ?>
	<button id="submit-election-results">Submit Election Results</button>
	<p>This action is final, you can not edit results once you submit them. <br>Please double check that they're complete!</p>
<?php } ?>