<?php

return array(

	// Charset to use for HTML encoding
	'charset'				=> 'UTF-8',
	
	// int	Total rows count
	'count'				=> 0,

	// int	Current page
	'current_page'			=> NULL,
	
	// int	Current first item offset
	'current_first_item' 	=> NULL,
	
	// int	Current first page number
	'current_first_page' 	=> NULL,
	
	// int	Current last item offset
	'current_last_item' 	=> NULL,
	
	// int	Current last page number
	'current_last_page' 	=> NULL,
	
	// int	Current next page number
	'current_next_page' 	=> NULL,
	
	// int	Current previous page number
	'current_prev_page' 	=> NULL,
	
	// int	Total pages
	'current_total_pages'	=> NULL,
	
	// bool	Hide pagination if it's one page?
	'_hide'					=> TRUE,

	// string	Language to use
	'lang'					=> 'en',
	
	// int	How many records to display?
	'limit' 				=> 15,
	
	// int	Offset
	'offset' 				=> 0,
	
	// bool	Show first page in URL?
	'show_first_page'		=> NULL,
	
	// string	Which skin to use?
	'skin'					=> NULL,
	
	// string	Which colour to use?
	'color'					=> NULL,
	
	// string	Name of the template to use
	'template'				=> 'basic',
	
	// string	Name of the $_GET key to use
	'query_key'				=> 'page',
);
