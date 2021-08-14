<?php
switch( $g_active_language ) {
	case 'chinese_simplified':
		# custom fields
		$s_field_carrier = '交通工具';
		$s_field_province = '省份';
		$s_field_txtype = '交易类型';
		break;
	case 'chinese_traditional':
		# custom fields
		$s_field_carrier = '交通工具';
		$s_field_province = '省份';
		$s_field_txtype = '交易類別';
		break;
	case 'japanese':
		# custom fields
		$s_field_carrier = '交通機関';
		$s_field_province = 'エリア';
		$s_field_txtype = 'タイプ';
		break;
	default:
		# custom fields
		$s_field_carrier = 'Carrier';
		$s_field_province = 'Province';
		$s_field_txtype = 'Type';
		break;
}
