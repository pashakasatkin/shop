<?php


function ssb_reddit_generate_link( $url ) {
	$request_url = 'https://www.reddit.com/api/info.json?url=' . $url;
	return $request_url;
}


function ssb_format_reddit_response( $response ) {
	$response = json_decode( $response, true );
	$score = 0;
	foreach ( $response['data']['children'] as $child ) {
		$score += $child['data']['score']; 
	}

	return $score;
}
