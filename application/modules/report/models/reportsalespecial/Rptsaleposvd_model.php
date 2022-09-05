<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptsaleposvd_model extends CI_Model {

    // Functionality: Call Stored Procedure
    // Parameters:  Function Parameter
    // Creator: 20/12/2019 Nonpaiwch(petch)
    // Last Modified :
    // Return : Status Return Call Stored Procedure
    // Return Type: Array
    public function FSnMExecStoreReports($paDataFilter) {
        $nLangID      = $paDataFilter['nLangID'];
        $tComName     = $paDataFilter['tCompName'];
        $tRptCode     = $paDataFilter['tRptCode'];
        $tUserSession = $paDataFilter['tUserSession'];

        // สาขา
        $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']); 
        // ร้านค้า
        $tShpCodeSelect = ($paDataFilter['bShpStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tShpCodeSelect']);
        // กลุ่มธุรกิจ
        $tMerCodeSelect = ($paDataFilter['bMerStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tMerCodeSelect']);
        // ประเภทเครื่องจุดขาย
        $tPosCodeSelect = ($paDataFilter['bPosStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tPosCodeSelect']);
 
        $tCallStore = "{CALL SP_RPTxDailySaleAmount(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}";
      
        $aDataStore = array(
            'pnLngID'       => $nLangID,
            'ptComName'     => $tComName,
            'ptRptCode'     => $tRptCode,
            'ptUsrSession'   => $tUserSession,
            'pnFilterType'  => $paDataFilter['tTypeSelect'], 

            //สาขา
            'ptBchF'        => $paDataFilter['tBchCodeFrom'],
            'ptBchT'        => $paDataFilter['tBchCodeTo'],
            'ptBchL'        => $tBchCodeSelect,

            //ร้านค้า
            'ptShpF'        => $paDataFilter['tShpCodeFrom'],
            'ptShpT'        => $paDataFilter['tShpCodeTo'],
            'ptShpL'        => $tShpCodeSelect,
             
            //เครื่องจุดขาย
            'ptPosF'   => $paDataFilter['tPosCodeFrom'],
            'ptPosT'   => $paDataFilter['tPosCodeTo'],    
            'ptPosL'   =>  $tPosCodeSelect,

            //กลุ่มธุระกิจ
            'ptMerF'        => $paDataFilter['tMerCodeFrom'],
            'ptMerT'        => $paDataFilter['tMerCodeTo'],
            'ptMerL'        =>  $tMerCodeSelect,

            //ประเภทการชำระเงิน
            'ptRcvF'        => $paDataFilter['tRcvCodeFrom'],
            'ptRcvT'        => $paDataFilter['tRcvCodeTo'],

            //วันที่
            'ptDocDateF'    => $paDataFilter['tDocDateFrom'],
            'ptDocDateT'    => $paDataFilter['tDocDateTo'],
         
            'pnResult'      => 0
        );

        $oQuery = $this->db->query($tCallStore, $aDataStore);
        if ($oQuery !== FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }


    }

    // Functionality: Get Data Page Co
    // Parameters:  Function Parameter
    // Creator: 20/12/2019 Nonpaiwch(petch)
    // Last Modified : 
    // Return : Array Data Page Nation
    // Return Type: Array
    public function FMaMRPTPagination($paDataWhere) {
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tAppType       = $paDataWhere['aDataFilter']['tPosType'];
        $tSQL = "   SELECT
                        COUNT(DTTMP.FTRptCode) AS rnCountPage
                    FROM TRPTPTTSpcSaleAmountTmp AS DTTMP WITH(NOLOCK)
                    WHERE 1=1
                    AND DTTMP.FTComName    = '$tComName'
                    AND DTTMP.FTRptCode    = '$tRptCode'
                    AND DTTMP.FTUsrSession = '$tUsrSession'
                    ";
        if($tAppType != ""){
            $tSQL .= " AND DTTMP.FNAppType = '$tAppType'";
        }
        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        $nPage = $paDataWhere['nPage'];
        $nPerPage = $paDataWhere['nPerPage'];
        $nPrevPage = $nPage - 1;
        $nNextPage = $nPage + 1;
        $nRowIDStart = (($nPerPage * $nPage) - $nPerPage);
        if ($nRptAllRecord <= $nPerPage) {
            $nTotalPage = 1;
        } else if (($nRptAllRecord % $nPerPage) == 0) {
            $nTotalPage = ($nRptAllRecord / $nPerPage);
        } else {
            $nTotalPage = ($nRptAllRecord / $nPerPage) + 1;
            $nTotalPage = (int) $nTotalPage;
        }

        // get rowid end
        $nRowIDEnd = $nPerPage * $nPage;
        if ($nRowIDEnd > $nRptAllRecord) {
            $nRowIDEnd = $nRptAllRecord;
        }

        $aRptMemberDet = array(
            "nTotalRecord" => $nRptAllRecord,
            "nTotalPage" => $nTotalPage,
            "nDisplayPage" => $paDataWhere['nPage'],
            "nRowIDStart" => $nRowIDStart,
            "nRowIDEnd" => $nRowIDEnd,
            "nPrevPage" => $nPrevPage,
            "nNextPage" => $nNextPage
        );
        unset($oQuery);
        return $aRptMemberDet;
    }

    // Functionality: Get Data Page Co
    // Parameters:  Function Parameter
    // Creator: 20/12/2019 Nonpaiwch(petch)
    // Last Modified : 
    // Return : Array Data Page Nation
    // Return Type: Array
    public function FMxMRPTSetPriorityGroup($paDataWhere) {

    
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tAppType       = $paDataWhere['aDataFilter']['tPosType'];

        $tSQL = " 
            UPDATE DATAUPD SET 
                DATAUPD.FNRowPartID = B.PartID
            FROM TRPTPTTSpcSaleAmountTmp AS DATAUPD WITH(NOLOCK)
            INNER JOIN(
                SELECT
                    ROW_NUMBER() OVER(PARTITION BY FTBchCode ORDER BY FTBchCode DESC , FTPdtCode ASC) AS PartID,
                    FTRptRowSeq
                FROM TRPTPTTSpcSaleAmountTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName     = '$tComName'
                AND TMP.FTRptCode       = '$tRptCode'
                AND TMP.FTUsrSession    = '$tUsrSession'";
        if($tAppType != ""){
            $tSQL .= " AND TMP.FNAppType = '$tAppType'";
        }

        $tSQL .= "
            ) AS B
            ON DATAUPD.FTRptRowSeq = B.FTRptRowSeq
            AND DATAUPD.FTComName       = '$tComName'
            AND DATAUPD.FTRptCode       = '$tRptCode'
            AND DATAUPD.FTUsrSession    = '$tUsrSession'";
        if($tAppType != ""){
            $tSQL .= " AND DATAUPD.FNAppType = '$tAppType'";
        }

        $this->db->query($tSQL);
    }

    // Functionality: Get Data Report In Table Temp
    // Parameters:  Function Parameter
    // Creator: 20/12/2019 Nonpaiwch(petch)
    // Last Modified : -
    // Return : Array Data report
    // Return Type: Array
    public function FSaMGetDataReport($paDataWhere) {
        $nPage          = $paDataWhere['nPage'];
        // Call Data Pagination
        $aPagination    = $this->FMaMRPTPagination($paDataWhere);
        $nRowIDStart    = $aPagination["nRowIDStart"];
        $nRowIDEnd      = $aPagination["nRowIDEnd"];
        $nTotalPage     = $aPagination["nTotalPage"];
        $tComName       = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tUsrSession    = $paDataWhere['tUsrSessionID'];
        $tAppType       = $paDataWhere['aDataFilter']['tPosType'];

        // Set Priority
        $this->FMxMRPTSetPriorityGroup($paDataWhere);
       
        if ($nPage == $nTotalPage) {
            $tJoinFoooter = "SELECT
                    FTUsrSession            AS FTUsrSession_Footer,
                    SUM(FCXrcNet)           AS FCXrcNet_Footer,
                    SUM(FCXsdVatable)       AS FCXsdVatable_Footer,
                    SUM(FCXshVat)           AS FCXshVat_Footer,
                    SUM(FCXrcGrand)       AS FCXsdNetAfHD_Footer
                FROM TRPTPTTSpcSaleAmountTmp WITH(NOLOCK)
                WHERE 1=1
                AND FTComName       = '$tComName'
                AND FTRptCode       = '$tRptCode'
                AND FTUsrSession    = '$tUsrSession'";

            if($tAppType != ""){
                $tJoinFoooter .= " AND FNAppType = '$tAppType'";
            }

            $tJoinFoooter .= " GROUP BY FTUsrSession ) T ON L.FTUsrSession = T.FTUsrSession_Footer";
        }else{
            $tJoinFoooter = "   
                SELECT
                    '$tUsrSession' AS FTUsrSession_Footer,
                    0 AS FCXrcNet_Footer,
                    0 AS FCXsdVatable_Footer,
                    0 AS FCXshVat_Footer,
                    0 AS FCXsdNetAfHD_Footer
                ) T ON L.FTUsrSession = T.FTUsrSession_Footer";
        }

        $tSQL = "
                SELECT
                    L.*,
                    T.FCXrcNet_Footer,
                    T.FCXsdVatable_Footer,
                    T.FCXshVat_Footer,
                    T.FCXsdNetAfHD_Footer
                FROM (
                    SELECT
                        ROW_NUMBER () OVER ( ORDER BY FTBchCode ASC ) AS RowID,
                        A.* 
                    FROM TRPTPTTSpcSaleAmountTmp A WITH ( NOLOCK )
                    WHERE 1=1
                    AND A.FTComName     = '$tComName'
                    AND A.FTRptCode     = '$tRptCode'
                    AND A.FTUsrSession  = '$tUsrSession' ";

        if($tAppType != ""){
            $tSQL .= " AND FNAppType = '$tAppType'";
        }

                    $tSQL .= " ) AS L 
                LEFT JOIN ( 
                    " . $tJoinFoooter . "
                ";
         

        // WHERE เงื่อนไข Page
        $tSQL .= "   WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";
        $tSQL .= "  ORDER BY L.FTPosCode ASC  ,L.FDXshDocDate ASC";
        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aData = $oQuery->result_array();
        } else {
            $aData = NULL;
        }
        $aErrorList = array(
            "nErrInvalidPage" => ""
        );

        $aResualt = array(
            "aPagination" => $aPagination,
            "aRptData" => $aData,
            "aError" => $aErrorList
        );
        unset($oQuery);
        unset($aData);
        return $aResualt;
    }

    // Functionality: Count Data Report All
    // Parameters: Function Parameter
    // Creator: 20/12/2019 Nonpaiwch(petch)
    // Last Modified: -
    // Return: Data Report All
    // ReturnType: Array
    public function FSnMCountRowInTemp($paDataWhere) {

        $tUserSession   = $paDataWhere['tUserSession'];
        $tCompName      = $paDataWhere['tCompName'];
        $tRptCode       = $paDataWhere['tRptCode'];
        $tPosType       = $paDataWhere['aDataFilter']['tPosType'];
        $tSQL = "   SELECT 
                             COUNT(DTTMP.FTRptCode) AS rnCountPage
                         FROM TRPTPTTSpcSaleAmountTmp AS DTTMP WITH(NOLOCK)
                         WHERE 1 = 1
                         AND FTUsrSession    = '$tUserSession'
                         AND FTComName       = '$tCompName'
                         AND FTRptCode       = '$tRptCode'
                        "
                         ;
        if($tPosType != ''){
            $tSQL .= "   AND FNAppType       = '$tPosType'";
        }

        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        unset($oQuery);
        return $nRptAllRecord;
    }

}


