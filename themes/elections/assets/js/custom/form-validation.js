$('#_oa_election_unit_location').attr('required', true);
$('#_oa_candidate_address_address_2').attr('required', false);
$('#_oa_nomination_address_address_2').attr('required', false);
$('#_oa_election_unit_location').on('change', function() {
	var location = $(this).val();
	$('#_oa_election_unit_address_text').val(location);
});
if ( $('.cmb-form').length ) {
	$('.cmb-form').parsley();
}
