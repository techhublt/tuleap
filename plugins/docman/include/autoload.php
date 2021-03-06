<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload643ada3a72400709a2535e265edc5005($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'docman_actions' => '/Docman_Actions.class.php',
            'docman_actionsdeletevisitor' => '/Docman_ActionsDeleteVisitor.class.php',
            'docman_approvalreviewer' => '/Docman_ApprovalTableReviewer.class.php',
            'docman_approvaltable' => '/Docman_ApprovalTable.class.php',
            'docman_approvaltabledao' => '/Docman_ApprovalTableDao.class.php',
            'docman_approvaltablefactory' => '/Docman_ApprovalTableFactory.class.php',
            'docman_approvaltablefile' => '/Docman_ApprovalTable.class.php',
            'docman_approvaltablefiledao' => '/Docman_ApprovalTableDao.class.php',
            'docman_approvaltablefilefactory' => '/Docman_ApprovalTableFactory.class.php',
            'docman_approvaltableitem' => '/Docman_ApprovalTable.class.php',
            'docman_approvaltableitemdao' => '/Docman_ApprovalTableDao.class.php',
            'docman_approvaltableitemfactory' => '/Docman_ApprovalTableFactory.class.php',
            'docman_approvaltablenotificationcycle' => '/Docman_ApprovalTableNotificationCycle.class.php',
            'docman_approvaltablereminder' => '/Docman_ApprovalTableReminder.class.php',
            'docman_approvaltablereviewerdao' => '/Docman_ApprovalTableReviewerDao.class.php',
            'docman_approvaltablereviewerfactory' => '/Docman_ApprovalTableReviewerFactory.class.php',
            'docman_approvaltableversionned' => '/Docman_ApprovalTable.class.php',
            'docman_approvaltableversionnedfactory' => '/Docman_ApprovalTableFactory.class.php',
            'docman_approvaltablewiki' => '/Docman_ApprovalTable.class.php',
            'docman_approvaltablewikidao' => '/Docman_ApprovalTableDao.class.php',
            'docman_approvaltablewikifactory' => '/Docman_ApprovalTableFactory.class.php',
            'docman_builditemmappingvisitor' => '/Docman_BuildItemMappingVisitor.class.php',
            'docman_cloneitemsvisitor' => '/Docman_CloneItemsVisitor.class.php',
            'docman_controller' => '/Docman_Controller.class.php',
            'docman_coreactions' => '/Docman_CoreActions.class.php',
            'docman_corecontroller' => '/Docman_CoreController.class.php',
            'docman_document' => '/Docman_Document.class.php',
            'docman_embeddedfile' => '/Docman_EmbeddedFile.class.php',
            'docman_empty' => '/Docman_Empty.class.php',
            'docman_error_permissiondenied' => '/Docman_Error_PermissionDenied.class.php',
            'docman_expandallhierarchyvisitor' => '/Docman_ExpandAllHierarchyVisitor.class.php',
            'docman_file' => '/Docman_File.class.php',
            'docman_filestorage' => '/Docman_FileStorage.class.php',
            'docman_filter' => '/Docman_Filter.class.php',
            'docman_filterdao' => '/Docman_FilterDao.class.php',
            'docman_filterdate' => '/Docman_Filter.class.php',
            'docman_filterdateadvanced' => '/Docman_Filter.class.php',
            'docman_filterfactory' => '/Docman_FilterFactory.class.php',
            'docman_filterglobaltext' => '/Docman_Filter.class.php',
            'docman_filteritemtype' => '/Docman_Filter.class.php',
            'docman_filteritemtypeadvanced' => '/Docman_Filter.class.php',
            'docman_filterlist' => '/Docman_Filter.class.php',
            'docman_filterlistadvanced' => '/Docman_Filter.class.php',
            'docman_filterowner' => '/Docman_Filter.class.php',
            'docman_filtertext' => '/Docman_Filter.class.php',
            'docman_folder' => '/Docman_Folder.class.php',
            'docman_folderfactory' => '/Docman_FolderFactory.class.php',
            'docman_htmlfilter' => '/Docman_HtmlFilter.class.php',
            'docman_htmlfilterdate' => '/Docman_HtmlFilter.class.php',
            'docman_htmlfilterdateadvanced' => '/Docman_HtmlFilter.class.php',
            'docman_htmlfilterfactory' => '/Docman_HtmlFilter.class.php',
            'docman_htmlfilterlist' => '/Docman_HtmlFilter.class.php',
            'docman_htmlfilterlistadvanced' => '/Docman_HtmlFilter.class.php',
            'docman_htmlfiltertext' => '/Docman_HtmlFilter.class.php',
            'docman_httpactions' => '/Docman_HTTPActions.class.php',
            'docman_httpcontroller' => '/Docman_HTTPController.class.php',
            'docman_icons' => '/Docman_Icons.class.php',
            'docman_item' => '/Docman_Item.class.php',
            'docman_itemaction' => '/Docman_ItemAction.class.php',
            'docman_itemactionapproval' => '/Docman_ItemAction.class.php',
            'docman_itemactioncopy' => '/Docman_ItemAction.class.php',
            'docman_itemactioncut' => '/Docman_ItemAction.class.php',
            'docman_itemactiondelete' => '/Docman_ItemAction.class.php',
            'docman_itemactiondetails' => '/Docman_ItemAction.class.php',
            'docman_itemactionhistory' => '/Docman_ItemAction.class.php',
            'docman_itemactionmove' => '/Docman_ItemAction.class.php',
            'docman_itemactionnewdocument' => '/Docman_ItemAction.class.php',
            'docman_itemactionnewfolder' => '/Docman_ItemAction.class.php',
            'docman_itemactionnewversion' => '/Docman_ItemAction.class.php',
            'docman_itemactionnotifications' => '/Docman_ItemAction.class.php',
            'docman_itemactionpaste' => '/Docman_ItemAction.class.php',
            'docman_itemactionpermissions' => '/Docman_ItemAction.class.php',
            'docman_itemactionupdate' => '/Docman_ItemAction.class.php',
            'docman_itemdao' => '/Docman_ItemDao.class.php',
            'docman_itemfactory' => '/Docman_ItemFactory.class.php',
            'docman_link' => '/Docman_Link.class.php',
            'docman_listmetadata' => '/Docman_Metadata.class.php',
            'docman_lockdao' => '/Docman_LockDao.class.php',
            'docman_lockfactory' => '/Docman_LockFactory.class.php',
            'docman_log' => '/Docman_Log.class.php',
            'docman_logdao' => '/Docman_LogDao.class.php',
            'docman_metadata' => '/Docman_Metadata.class.php',
            'docman_metadatacomparator' => '/Docman_MetadataComparator.class.php',
            'docman_metadatadao' => '/Docman_MetadataDao.class.php',
            'docman_metadatafactory' => '/Docman_MetadataFactory.class.php',
            'docman_metadatahtml' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmldate' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmlembeddedfile' => '/view/Docman_View_GetSpecificFieldsVisitor.class.php',
            'docman_metadatahtmlempty' => '/view/Docman_View_GetSpecificFieldsVisitor.class.php',
            'docman_metadatahtmlfactory' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmlfile' => '/view/Docman_View_GetSpecificFieldsVisitor.class.php',
            'docman_metadatahtmllink' => '/view/Docman_View_GetSpecificFieldsVisitor.class.php',
            'docman_metadatahtmllist' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmlobsolescence' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmlowner' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmlstring' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmltext' => '/Docman_MetadataHtml.class.php',
            'docman_metadatahtmlwiki' => '/view/Docman_View_GetSpecificFieldsVisitor.class.php',
            'docman_metadatalistofvalueselement' => '/Docman_MetadataListOfValuesElement.class.php',
            'docman_metadatalistofvalueselementdao' => '/Docman_MetadataListOfValuesElementDao.class.php',
            'docman_metadatalistofvalueselementfactory' => '/Docman_MetadataListOfValuesElementFactory.class.php',
            'docman_metadatasqlquerychunk' => '/Docman_MetadataSqlQueryChunk.class.php',
            'docman_metadatavalue' => '/Docman_MetadataValue.class.php',
            'docman_metadatavaluedao' => '/Docman_MetadataValueDao.class.php',
            'docman_metadatavaluefactory' => '/Docman_MetadataValueFactory.class.php',
            'docman_metadatavaluelist' => '/Docman_MetadataValue.class.php',
            'docman_metadatavaluescalar' => '/Docman_MetadataValue.class.php',
            'docman_metametadatahtml' => '/Docman_MetaMetadataHtml.class.php',
            'docman_mimetypedetector' => '/Docman_MIMETypeDetector.class.php',
            'docman_nodetorootvisitor' => '/Docman_NodeToRootVisitor.class.php',
            'docman_notificationsdao' => '/Docman_NotificationsDao.class.php',
            'docman_notificationsmanager' => '/Docman_NotificationsManager.class.php',
            'docman_notificationsmanager_add' => '/Docman_NotificationsManager_Add.class.php',
            'docman_notificationsmanager_delete' => '/Docman_NotificationsManager_Delete.class.php',
            'docman_notificationsmanager_move' => '/Docman_NotificationsManager_Move.class.php',
            'docman_notificationsmanager_subscribers' => '/Docman_NotificationsManager_Subscribers.class.php',
            'docman_path' => '/Docman_Path.class.php',
            'docman_permissionsexport' => '/Docman_PermissionsExport.class.php',
            'docman_permissionsitemmanager' => '/Docman_PermissionsItemManager.class.php',
            'docman_permissionsmanager' => '/Docman_PermissionsManager.class.php',
            'docman_permissionsmanagerdao' => '/Docman_PermissionsManagerDao.class.php',
            'docman_report' => '/Docman_Report.class.php',
            'docman_reportcolumn' => '/Docman_ReportColumn.class.php',
            'docman_reportcolumnfactory' => '/Docman_ReportColumnFactory.class.php',
            'docman_reportcolumnlist' => '/Docman_ReportColumn.class.php',
            'docman_reportcolumnlocation' => '/Docman_ReportColumn.class.php',
            'docman_reportcolumntitle' => '/Docman_ReportColumn.class.php',
            'docman_reportdao' => '/Docman_ReportDao.class.php',
            'docman_reportfactory' => '/Docman_ReportFactory.class.php',
            'docman_reporthtml' => '/Docman_ReportHtml.class.php',
            'docman_settingsbo' => '/Docman_SettingsBo.class.php',
            'docman_settingsdao' => '/Docman_SettingsDao.class.php',
            'docman_soapactions' => '/Docman_SOAPActions.class.php',
            'docman_soapcontroller' => '/Docman_SOAPController.class.php',
            'docman_sqlfilter' => '/Docman_SqlFilter.class.php',
            'docman_sqlfilterdate' => '/Docman_SqlFilter.class.php',
            'docman_sqlfilterdateadvanced' => '/Docman_SqlFilter.class.php',
            'docman_sqlfilterfactory' => '/Docman_SqlFilter.class.php',
            'docman_sqlfilterglobaltext' => '/Docman_SqlFilter.class.php',
            'docman_sqlfilterlistadvanced' => '/Docman_SqlFilter.class.php',
            'docman_sqlfilterowner' => '/Docman_SqlFilter.class.php',
            'docman_sqlfiltertext' => '/Docman_SqlFilter.class.php',
            'docman_sqlreportcolumn' => '/Docman_SqlReportColumn.class.php',
            'docman_sqlreportcolumnfactory' => '/Docman_SqlReportColumn.class.php',
            'docman_subitemsremovalvisitor' => '/Docman_SubItemsRemovalVisitor.class.php',
            'docman_subitemswritablevisitor' => '/Docman_SubItemsWritableVisitor.class.php',
            'docman_token' => '/Docman_Token.class.php',
            'docman_tokendao' => '/Docman_TokenDao.class.php',
            'docman_validatefilter' => '/Docman_ValidateFilter.class.php',
            'docman_validatefilterdate' => '/Docman_ValidateFilter.class.php',
            'docman_validatefilterfactory' => '/Docman_ValidateFilter.class.php',
            'docman_validatemetadataisnotempty' => '/Docman_MetadataHtml.class.php',
            'docman_validatemetadatalistisnotempty' => '/Docman_MetadataHtml.class.php',
            'docman_validatepresenceof' => '/Docman_Validator.class.php',
            'docman_validateupload' => '/Docman_ValidateUpload.class.php',
            'docman_validatevaluenotempty' => '/Docman_Validator.class.php',
            'docman_validator' => '/Docman_Validator.class.php',
            'docman_version' => '/Docman_Version.class.php',
            'docman_versiondao' => '/Docman_VersionDao.class.php',
            'docman_versionfactory' => '/Docman_VersionFactory.class.php',
            'docman_view_admin' => '/view/Docman_View_Admin.class.php',
            'docman_view_admin_lockinfos' => '/view/Docman_View_Admin_LockInfos.class.php',
            'docman_view_admin_metadata' => '/view/Docman_View_Admin_Metadata.class.php',
            'docman_view_admin_metadatadetails' => '/view/Docman_View_Admin_MetadataDetails.class.php',
            'docman_view_admin_metadatadetailsupdatelove' => '/view/Docman_View_Admin_MetadataDetailsUpdateLove.class.php',
            'docman_view_admin_metadataimport' => '/view/Docman_View_Admin_MetadataImport.class.php',
            'docman_view_admin_obsolete' => '/view/Docman_View_Admin_Obsolete.class.php',
            'docman_view_admin_permissions' => '/view/Docman_View_Admin_Permissions.class.php',
            'docman_view_admin_view' => '/view/Docman_View_Admin_View.class.php',
            'docman_view_ajaxreferencetooltip' => '/view/Docman_View_AjaxReferenceTooltip.class.php',
            'docman_view_ajaxreferencetooltiperror' => '/view/Docman_View_AjaxReferenceTooltipError.class.php',
            'docman_view_approvalcreate' => '/view/Docman_View_ApprovalCreate.class.php',
            'docman_view_browse' => '/view/Docman_View_Browse.class.php',
            'docman_view_delete' => '/view/Docman_View_Delete.class.php',
            'docman_view_details' => '/view/Docman_View_Details.class.php',
            'docman_view_display' => '/view/Docman_View_Display.class.php',
            'docman_view_docman' => '/view/Docman_View_Docman.class.php',
            'docman_view_docmanerror' => '/view/Docman_View_DocmanError.class.php',
            'docman_view_download' => '/view/Docman_View_Download.class.php',
            'docman_view_edit' => '/view/Docman_View_Edit.class.php',
            'docman_view_embedded' => '/view/Docman_View_Embedded.class.php',
            'docman_view_empty' => '/view/Docman_View_Empty.class.php',
            'docman_view_error' => '/view/Docman_View_Error.class.php',
            'docman_view_extra' => '/view/Docman_View_Extra.class.php',
            'docman_view_getactiononiconvisitor' => '/view/Docman_View_GetActionOnIconVisitor.class.php',
            'docman_view_getclassforlinkvisitor' => '/view/Docman_View_GetClassForLinkVisitor.class.php',
            'docman_view_getfieldsvisitor' => '/view/Docman_View_GetFieldsVisitor.class.php',
            'docman_view_getmenuitemsvisitor' => '/view/Docman_View_GetMenuItemsVisitor.class.php',
            'docman_view_getshowviewvisitor' => '/view/Docman_View_GetShowViewVisitor.class.php',
            'docman_view_getspecificfieldsvisitor' => '/view/Docman_View_GetSpecificFieldsVisitor.class.php',
            'docman_view_header' => '/view/Docman_View_Header.class.php',
            'docman_view_icons' => '/view/Docman_View_Icons.class.php',
            'docman_view_install' => '/view/Docman_View_Install.class.php',
            'docman_view_installed' => '/view/Docman_View_Installed.class.php',
            'docman_view_itemdetails' => '/view/Docman_View_ItemDetails.class.php',
            'docman_view_itemdetailssection' => '/view/Docman_View_ItemDetailsSection.class.php',
            'docman_view_itemdetailssectionactions' => '/view/Docman_View_ItemDetailsSectionActions.class.php',
            'docman_view_itemdetailssectionapproval' => '/view/Docman_View_ItemDetailsSectionApproval.class.php',
            'docman_view_itemdetailssectionapprovalcreate' => '/view/Docman_View_ItemDetailsSectionApprovalCreate.class.php',
            'docman_view_itemdetailssectiondelete' => '/view/Docman_View_ItemDetailsSectionDelete.class.php',
            'docman_view_itemdetailssectioneditproperties' => '/view/Docman_View_ItemDetailsSectionEditProperties.class.php',
            'docman_view_itemdetailssectionhistory' => '/view/Docman_View_ItemDetailsSectionHistory.class.php',
            'docman_view_itemdetailssectionmove' => '/view/Docman_View_ItemDetailsSectionMove.class.php',
            'docman_view_itemdetailssectionnewversion' => '/view/Docman_View_ItemDetailsSectionNewVersion.class.php',
            'docman_view_itemdetailssectionnotifications' => '/view/Docman_View_ItemDetailsSectionNotifications.class.php',
            'docman_view_itemdetailssectionpaste' => '/view/Docman_View_ItemDetailsSectionPaste.class.php',
            'docman_view_itemdetailssectionpermissions' => '/view/Docman_View_ItemDetailsSectionPermissions.class.php',
            'docman_view_itemdetailssectionproperties' => '/view/Docman_View_ItemDetailsSectionProperties.class.php',
            'docman_view_itemdetailssectionreferences' => '/view/Docman_View_ItemDetailsSectionReferences.class.php',
            'docman_view_itemdetailssectionstatistics' => '/view/Docman_View_ItemDetailsSectionStatistics.class.php',
            'docman_view_itemdetailssectionupdate' => '/view/Docman_View_ItemDetailsSectionUpdate.class.php',
            'docman_view_itemranking' => '/view/Docman_View_ItemRanking.class.php',
            'docman_view_itemtreeulvisitor' => '/view/Docman_View_ItemTreeUlVisitor.class.php',
            'docman_view_lovedetails' => '/view/Docman_View_LoveDetails.class.php',
            'docman_view_move' => '/view/Docman_View_Move.class.php',
            'docman_view_new' => '/view/Docman_View_New.class.php',
            'docman_view_new_folderselection' => '/view/Docman_View_New_FolderSelection.class.php',
            'docman_view_newdocument' => '/view/Docman_View_NewDocument.class.php',
            'docman_view_newfolder' => '/view/Docman_View_NewFolder.class.php',
            'docman_view_newversion' => '/view/Docman_View_NewVersion.class.php',
            'docman_view_parentstree' => '/view/Docman_View_ParentsTree.class.php',
            'docman_view_paste' => '/view/Docman_View_Paste.class.php',
            'docman_view_pasteinprogress' => '/view/Docman_View_PasteInProgress.class.php',
            'docman_view_permissiondeniederror' => '/view/Docman_View_PermissionDeniedError.class.php',
            'docman_view_permissionsforitem' => '/view/Docman_View_PermissionsForItem.class.php',
            'docman_view_positionwithinfolder' => '/view/Docman_View_PositionWithinFolder.class.php',
            'docman_view_projecterror' => '/view/Docman_View_ProjectError.class.php',
            'docman_view_projectheader' => '/view/Docman_View_ProjectHeader.class.php',
            'docman_view_rawtree' => '/view/Docman_View_RawTree.class.php',
            'docman_view_redirect' => '/view/Docman_View_Redirect.class.php',
            'docman_view_redirectaftercrud' => '/view/Docman_View_RedirectAfterCrud.class.php',
            'docman_view_reportsettings' => '/view/Docman_View_ReportSettings.class.php',
            'docman_view_soap_list' => '/view/soap/Docman_View_SOAP_List.class.php',
            'docman_view_soap_search' => '/view/soap/Docman_View_SOAP_Search.class.php',
            'docman_view_soap_soap' => '/view/soap/Docman_View_SOAP_SOAP.class.php',
            'docman_view_table' => '/view/Docman_View_Table.class.php',
            'docman_view_toolbarnewdocumentvisitor' => '/view/Docman_View_ToolbarNewDocumentVisitor.class.php',
            'docman_view_tree' => '/view/Docman_View_Tree.class.php',
            'docman_view_update' => '/view/Docman_View_Update.class.php',
            'docman_view_view' => '/view/Docman_View_View.class.php',
            'docman_widget_embedded' => '/Docman_Widget_Embedded.class.php',
            'docman_widget_mydocman' => '/Docman_Widget_MyDocman.class.php',
            'docman_widget_mydocmansearch' => '/Docman_Widget_MyDocmanSearch.class.php',
            'docman_widget_myembedded' => '/Docman_Widget_MyEmbedded.class.php',
            'docman_widget_projectembedded' => '/Docman_Widget_ProjectEmbedded.class.php',
            'docman_wiki' => '/Docman_Wiki.class.php',
            'docman_wikicontroller' => '/Docman_WikiController.class.php',
            'docman_wikirequest' => '/Docman_WikiRequest.class.php',
            'docmanonefolderiswriteable' => '/DocmanOneFolderIsWriteable.class.php',
            'docmanplugin' => '/docmanPlugin.class.php',
            'docmanplugindescriptor' => '/DocmanPluginDescriptor.class.php',
            'docmanplugininfo' => '/DocmanPluginInfo.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload643ada3a72400709a2535e265edc5005');
// @codeCoverageIgnoreEnd