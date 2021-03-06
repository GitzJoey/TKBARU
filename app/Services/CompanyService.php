<?php
/**
 * Created by PhpStorm.
 * User: gitzj
 * Date: 3/22/2018
 * Time: 12:50 AM
 */

namespace App\Services;

interface CompanyService
{
    public function create(
        $name,
        $address,
        $latitude,
        $longitude,
        $phone_num,
        $fax_num,
        $tax_id,
        $status,
        $is_default,
        $frontweb,
        $image_filename,
        $remarks,
        $bank,
        $date_format,
        $time_format,
        $thousand_separator,
        $decimal_separator,
        $decimal_digit,
        $ribbon
    );
    public function read();
    public function update(
        $id,
        $name,
        $address,
        $latitude,
        $longitude,
        $phone_num,
        $fax_num,
        $tax_id,
        $status,
        $is_default,
        $frontweb,
        $image_filename,
        $remarks,
        $bank,
        $date_format,
        $time_format,
        $thousand_separator,
        $decimal_separator,
        $decimal_digit,
        $ribbon
    );
    public function delete($id);

    public function createDefaultCompany($companyName);

    public function setDefaultCompany($id);

    public function isEmptyCompanyTable();

    public function defaultStorePresent();

    public function getDefaultCompany();
}