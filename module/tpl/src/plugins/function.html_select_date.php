<?php
/*
 * Template Lite plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_select_date
 * Version:  1.3
 * Purpose:  Prints the dropdowns for date selection.
 * Author:   Andrei Zmievski
 *
 * ChangeLog: 1.0 initial release
 *            1.1 added support for +/- N syntax for begin
 *                and end year values. (Monte)
 *            1.2 added support for yyyy-mm-dd syntax for
 *                time value. (Jan Rosier)
 *            1.3 added support for choosing format for 
 *                month values (Gary Loescher)
 *            1.3.1 added support for choosing format for
 *                day values (Marcus Bointon)
 * Taken from the original Smarty
 * http://smarty.php.net
 * -------------------------------------------------------------
 */
function tpl_function_html_select_date($params, &$template_object)
{
	require_once("shared.make_timestamp.php");
	require_once("function.html_options.php");

    /* Default values. */
    $prefix          = "Date_";
    $start_year      = strftime("%Y");
    $end_year        = $start_year;
    $display_days    = true;
    $display_months  = true;
    $display_years   = true;
    $month_format    = "%B";
	$lang			 = 'en';
    /* Write months as numbers by default  GL */
    $month_value_format = "%m";
    $day_format      = "%02d";
    /* Write day values using this format MB */
    $day_value_format = "%d";
    $year_as_text    = false;
    /* Display years in reverse order? Ie. 2000,1999,.... */
    $reverse_years   = false;
    /* Should the select boxes be part of an array when returned from PHP?
       e.g. setting it to "birthday", would create "birthday[Day]",
       "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
    $field_array     = null;
    /* <select size>'s of the different <select> tags.
       If not set, uses default dropdown. */
    $day_size        = null;
    $month_size      = null;
    $year_size       = null;
    /* Unparsed attributes common to *ALL* the <select>/<input> tags.
       An example might be in the template: all_extra ='class ="foo"'. */
    $all_extra       = null;
    /* Separate attributes for the tags. */
    $day_extra       = null;
    $month_extra     = null;
    $year_extra      = null;
    $select_class    = null;
    /* Order in which to display the fields.
       "D" -> day, "M" -> month, "Y" -> year. */
    $field_order      = 'MDY';
    /* String printed between the different fields. */
    $field_separator = "\n";
	$time = time();
	/* if empty fields allowed */
	$year_empty = null;
	$month_empty = null;
	$day_empty = null;

	
    extract($params);
	//if($time == '0000-00-00'){ $time = date('Y-m-d'); }
	
	if(empty($year_empty) && empty($month_empty) && empty($day_empty) && $time == '0000-00-00')
	{
			$time = date('Y-m-d');
	}	
	
  	// If $time is not in format yyyy-mm-dd
  	if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $time))
	{
  		// then $time is empty or unix timestamp or mysql timestamp
  		// using smarty_make_timestamp to get an unix timestamp and
  		// strftime to make yyyy-mm-dd
  		
		if(!empty($year_empty) && !empty($month_empty) 
			&& !empty($day_empty) && empty($params['time'])
		)
		{
			$time = '0-0-0';
		}else
		{
			$time = strftime('%Y-%m-%d', tpl_make_timestamp($time));
		}
  	}
  	// Now split this in pieces, which later can be used to set the select
	//echo $params['time'].' - '.$time; exit;
  	$time = explode("-", $time);

  	// make syntax "+N" or "-N" work with start_year and end_year
  	if (preg_match('!^(\+|\-)\s*(\d+)$!', $end_year, $match))
	{
  		if ($match[1] == '+')
		{
  			$end_year = strftime('%Y') + $match[2];
  		}
		else
		{
  			$end_year = strftime('%Y') - $match[2];
  		}
  	}
  	if (preg_match('!^(\+|\-)\s*(\d+)$!', $start_year, $match))
	{
  		if ($match[1] == '+')
		{
  			$start_year = strftime('%Y') + $match[2];
  		}
		else
		{
  			$start_year = strftime('%Y') - $match[2];
  		}
  	}

    $field_order = strtoupper($field_order);
    $html_result = $month_result = $day_result = $year_result = "";

    if ($display_months)
	{
        $month_names = array();
        $month_values = array();

		if(!empty($month_empty)){
			$month_names[] = $month_empty;
			$month_values[] = '0';	
		}
		
        for ($i = 1; $i <= 12; $i++)
		{
			$f = MODULE.'/tpl/src/plugins/modifier.months.php';
            $m_name = strftime($month_format, mktime(0, 0, 0, $i, 1, 2000));
			if($month_format == '%m' && file_exists($f)){
				include_once($f);
				$m_name = tpl_modifier_months($i, $lang);
			}
			$month_names[] = $m_name;
            $month_values[] = strftime($month_value_format, mktime(0, 0, 0, $i, 1, 2000));
        }

        $month_result .= '<select';
        if (null !== $select_class){
	        $month_result .= ' class="'.$select_class.'" ';
		}
        $month_result .= ' name=';

        if (null !== $field_array)
		{
            $month_result .= '"' . $field_array . '[' . $prefix . 'Month]"';
        }
		else
		{
            $month_result .= '"' . $prefix . 'Month"';
        }
        if (null !== $month_size)
		{
            $month_result .= ' size="' . $month_size . '"';
        }
        if (null !== $month_extra)
		{
            $month_result .= ' ' . $month_extra;
        }
        if (null !== $all_extra)
		{
            $month_result .= ' ' . $all_extra;
        }
        $month_result .= '>'."\n";
		
		$selected = !empty($month_empty) ? $time[1] : $month_values[$time[1]-1];
		
        $month_result .= tpl_function_html_options(array(
								'output' => $month_names,
								'values' => $month_values,
								'selected' => $selected,
								'print_result' => false),
                            $template_object);
        $month_result .= '</select>';
    }

    if ($display_days)
	{
        $days = array();
		
		if(!empty($day_empty)){
			$days[] = $day_empty;
			$day_values[] = '0';	
		}
		
        for ($i = 1; $i <= 31; $i++)
		{
            $days[] = sprintf($day_format, $i);
            $day_values[] = sprintf($day_value_format, $i);
        }

        $day_result .= '<select';
        if (null !== $select_class){
	        $day_result .= ' class="'.$select_class.'" ';
		}
        $day_result .= ' name=';

        if (null !== $field_array)
		{
            $day_result .= '"' . $field_array . '[' . $prefix . 'Day]"';
        }
		else
		{
            $day_result .= '"' . $prefix . 'Day"';
        }
        if (null !== $day_size)
		{
            $day_result .= ' size="' . $day_size . '"';
        }
        if (null !== $all_extra)
		{
            $day_result .= ' ' . $all_extra;
        }
        if (null !== $day_extra)
		{
            $day_result .= ' ' . $day_extra;
        }
        $day_result .= '>'."\n";
        $day_result .= tpl_function_html_options(array('output'     => $days,
                                                          'values'     => $day_values,
                                                          'selected'   => $time[2],
                                                          'print_result' => false),
                                                    $template_object);
        $day_result .= '</select>';
    }

    if ($display_years)
	{
        if (null !== $field_array)
		{
            $year_name = $field_array . '[' . $prefix . 'Year]';
        }
		else
		{
            $year_name = $prefix . 'Year';
        }
        if ($year_as_text)
		{
            $year_result .= '<input type="text" name="' . $year_name . '" value="' . $time[0] . '" size="4" maxlength="4"';
            if (null !== $all_extra)
			{
                $year_result .= ' ' . $all_extra;
            }
            if (null !== $year_extra)
			{
                $year_result .= ' ' . $year_extra;
            }
            $year_result .= '>';
        }
		else
		{
            $years = range((int)$start_year, (int)$end_year);
            $year_values = range((int)$start_year, (int)$end_year);
			
			if(!empty($year_empty)){
				$years[] = $year_empty;
				$year_values[] = 0;
			}
			
            if ($reverse_years)
			{
                rsort($years, SORT_NUMERIC);
                rsort($year_values, SORT_NUMERIC);
            }

            $year_result .= '<select';
			if (null !== $select_class){
				$year_result .= ' class="'.$select_class.'" ';
			}
            $year_result .= ' name="' . $year_name . '"';

            if (null !== $year_size)
			{
                $year_result .= ' size="' . $year_size . '"';
            }
            if (null !== $all_extra)
			{
                $year_result .= ' ' . $all_extra;
            }
            if (null !== $year_extra)
			{
                $year_result .= ' ' . $year_extra;
            }
            $year_result .= '>'."\n";
			
			if($time[0] > $end_year){ $time[0] = $end_year; }
            $year_result .= tpl_function_html_options(array('output' => $years,
                                                               'values' => $year_values,
                                                               'selected'   => $time[0],
                                                               'print_result' => false),
                                                         $template_object);
            $year_result .= '</select>';
        }
    }

    // Loop thru the field_order field
    for ($i = 0; $i <= 2; $i++)
	{
		$c = substr($field_order, $i, 1);
		switch ($c)
		{
			case 'D':
				$html_result .= $day_result;
				break;

			case 'M':
				$html_result .= $month_result;
				break;

			case 'Y':
				$html_result .= $year_result;
				break;
		}
		// Add the field seperator
		if($i != 2)
		{
			$html_result .= $field_separator;
		}
	}
    return $html_result;
}

?>
