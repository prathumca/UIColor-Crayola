<?php

//
//*******
//
//	filename: crayola_source_generator.php
//	author: Zack Brown
//	date: 21/05/2013
//
//*******
//

require_once "crayola_colors.php";

$header_filename = "UIColor+Crayola.h";
$main_filename = "UIColor+Crayola.m";
$markdown_filename = "README.md";

$image_width = 32;
$image_height = 32;

$github_url = "https://raw.github.com/CaptainRedmuff/UIColor-Crayola/master/";

$header_file_meta = "//\n";
$header_file_meta .= "//*******\n";
$header_file_meta .= "//\n";
$header_file_meta .= "//\tfilename: $header_filename\n";
$header_file_meta .= "//\tauthor: Zack Brown\n";
$header_file_meta .= "//\tdate: " . date("j/m/Y", mktime()) . "\n";
$header_file_meta .= "//\n";
$header_file_meta .= "//*******\n";
$header_file_meta .= "//\n";
$header_file_meta .= "\n";
$header_file_meta .= "#import <UIKit/UIKit.h>\n";
$header_file_meta .= "\n";
$header_file_meta .= "@interface UIColor (Crayola)\n\n";

$header_file_footer_meta = "\n@end\n";

$main_file_meta = "//\n";
$main_file_meta .= "//*******\n";
$main_file_meta .= "//\n";
$main_file_meta .= "//\tfilename: $main_filename\n";
$main_file_meta .= "//\tauthor: Zack Brown\n";
$main_file_meta .= "//\tdate: " . date("j/m/Y", mktime()) . "\n";
$main_file_meta .= "//\n";
$main_file_meta .= "//*******\n";
$main_file_meta .= "//\n";
$main_file_meta .= "\n";
$main_file_meta .= "#import \"$header_filename\"\n";
$main_file_meta .= "\n";
$main_file_meta .= "@implementation UIColor (Crayola)\n\n";

$main_file_footer_meta = "@end\n";

$markdown_file_meta = "UIColor+Crayola\n";
$markdown_file_meta .= "===============\n\n";
$markdown_file_meta .= "UIColor category - because everybody loves wax crayons!\n\n";
$markdown_file_meta .= "List of " . count($colors) . " colors sourced from: <a href=\"http://en.wikipedia.org/wiki/Crayola_colors\" title=\"Crayola Colors\">List of Crayola crayon colors</a>\n\n";
$markdown_file_meta .= "<table>\n\n";

$markdown_file_footer_meta .= "</table>\n\n";
$markdown_file_footer_meta .= "===============\n\n";
$markdown_file_footer_meta .= "Contributors:\n";
$markdown_file_footer_meta .= "<a href=\"https://github.com/Abizern\" title=\"Abizern\">Abizern</a>\n";

$header_file_handle = fopen($header_filename, "w+");
$main_file_handle = fopen($main_filename, "w+");
$markdown_file_handle = fopen($markdown_filename, "w+");

fwrite($header_file_handle, $header_file_meta);
fwrite($main_file_handle, $main_file_meta);
fwrite($markdown_file_handle, $markdown_file_meta);

foreach($colors as $key => $value)
{
	$name = uicolor_name($value);
	$color = uicolor_from_hex($key);

	generate_image_from_hex($value, $key, $image_width, $image_height);

	$header_code = "+ (instancetype)$name;\n";

	$main_code = "+ (instancetype)$name\n";
	$main_code .= "{\n";
	$main_code .= "\treturn " . crayola_color_from_hex($key) . ";\n";
	$main_code .= "}\n\n";

	$markdown_code = uicolor_table_row($value, $key, $image_width, $image_height, $github_url . "images/");

	fwrite($header_file_handle, $header_code);
	fwrite($main_file_handle, $main_code);
	fwrite($markdown_file_handle, $markdown_code);
}


$main_code = "+ (instancetype)crayolaColorWithRed:(CGFloat)red green:(CGFloat)green blue:(CGFloat)blue alpha:(CGFloat)alpha\n";
$main_code .= "{\n";
$main_code .= "\tstatic NSCache *cache = nil;\n\n";
$main_code .= "\tif(!cache)\n";
$main_code .= "\t{\n";
$main_code .= "\t\tcache = [NSCache new];\n\n";
$main_code .= "\t\t[cache setName:@\"UIColor+Crayola\"];\n";
$main_code .= "\t}\n\n";
$main_code .= "\tNSString *cacheKey = [[self class] cacheKeyWithRed:red green:green blue:blue alpha:alpha];\n\n";
$main_code .= "\tUIColor *color = [cache objectForKey:cacheKey];\n\n";
$main_code .= "\tif(!color)\n";
$main_code .= "\t{\n";
$main_code .= "\t\tcolor = [UIColor colorWithRed:red green:green blue:blue alpha:alpha];\n\n";
$main_code .= "\t\t[cache setObject:color forKey:cacheKey];\n";
$main_code .= "\t}\n\n";
$main_code .= "\treturn color;\n";
$main_code .= "}\n\n";
$main_code .= "+ (NSString *)cacheKeyWithRed:(CGFloat)red green:(CGFloat)green blue:(CGFloat)blue alpha:(CGFloat)alpha\n";
$main_code .= "{\n";
$main_code .= "\treturn [NSString stringWithFormat:@\"%.2f%.2f%.2f%.2f\", red, green, blue, alpha];\n";
$main_code .= "}\n\n";

fwrite($main_file_handle, $main_code);

fwrite($header_file_handle, $header_file_footer_meta);
fwrite($main_file_handle, $main_file_footer_meta);
fwrite($markdown_file_handle, $markdown_file_footer_meta);

fclose($header_file_handle);
fclose($main_file_handle);
fclose($markdown_file_handle);

?>