<?php

$route['admin/document/manage'] = "property_documents/admin/document/manage";
$route['admin/document/upload_document_callback_func'] = "property_documents/admin/document/upload_document_callback_func";
$route['admin/document/delete_documents_callback_func'] = "property_documents/admin/document/delete_documents_callback_func";
$route['admin/document/add_doc_from_document_library_ajax_callback_func'] = "property_documents/admin/document/add_doc_from_document_library_ajax_callback_func";

$route['admin/document/type'] = "property_documents/admin/document/type";
$route['admin/document/type/(:any)'] = "property_documents/admin/document/type/$1";
$route['admin/document/delete_type/(:any)'] = "property_documents/admin/document/delete_type/$1";