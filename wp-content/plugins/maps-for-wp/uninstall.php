<?php 
if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;
// �������� �������� �������. ������� �� ���� ������� ����� � ��� ���������.

function mfwp_delete_plugin() {
	global $wpdb; // ���������� ����� wordpress ��� ������ � ��

	delete_option('mfwp_version');
	delete_option('mfwp_zoom_ManyPoints'); // ������� ����� ��� ����� �����
	delete_option('mfwp_center_lat_ManyPoints'); // ������ ����� ��� ����� �����
	delete_option('mfwp_center_lon_ManyPoints'); // ������� ����� ��� ����� �����
	
	delete_option('mfwp_zoom_OnePoint'); // ������� ����� ��� ���� �����
	delete_option('mfwp_lat_Create'); // ������ ����� ��� �������� ��� ���� �����
	delete_option('mfwp_long_Create'); // ������� ����� ��� �������� ��� ���� �����
	delete_option('mfwp_point_img'); // �������� �������
		
	delete_option('mfwp_code_hidden'); // ������������ �������������
	delete_option('mfwp_code_header'); // ������������ �������������
	delete_option('mfwp_code_body'); // ������������ �������������
	delete_option('mfwp_code_footer'); // ������������ �������������
	delete_option('mfwp_add_post'); // ������������ �������������
	delete_option('mfwp_add_page'); // ������������ �������������
	delete_option('mfwp_add_tax'); // ������������ �������������
}

mfwp_delete_plugin(); // �������� ������� �������� �������
?>