<?php

function at_home_view_user_submissions($user){
	$pager_count = 0;
	$node = node_load(AT_HOME_PROGRAM_FINDER_FORM_NID);

	// Get all the submissions for the user.
	$header = theme('webform_results_table_header', array('node' => $node));
	$submissions = webform_get_submissions(array('nid' => $node->nid,'uid' => $user->uid), $header, $pager_count);
	$total_count = webform_get_submission_count($node->nid, $user->uid);

	$output = theme('webform_results_table', array('node' => $node, 'components' => $node->webform['components'], 'submissions' => $submissions, 'total_count' => $total_count, 'pager_count' => $pager_count));
	if ($pager_count) {
	$output .= theme('pager');
	}
	return $output;
}