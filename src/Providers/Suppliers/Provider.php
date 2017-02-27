<?php namespace Nivv\Fortie\Providers\Suppliers;

/*

   Copyright 2015 Andreas Göransson

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/

use Nivv\Fortie\Providers\ProviderBase;


class Provider extends ProviderBase {


  protected $attributes = [
    'Url',
    'Active',
    'Address1',
    'Address2',
    'Bank',
    'BankAccountNumber',
    'BG',
    'BIC',
    'BranchCode',
    'City',
    'ClearingNumber',
    'Comments',
    'CostCenter',
    'Country',
    'CountryCode',
    'Currency',
    'DisablePaymentFile',
    'Email',
    'Fax',
    'IBAN',
    'Name',
    'OrganisationNumber',
    'OurReference',
    'OurCustomerNumber',
    'PG',
    'Phone1',
    'Phone2',
    'PreDefinedAccount',
    'Project',
    'SupplierNumber',
    'TermsOfPayment',
    'VATNumber',
    'VATType',
    'VisitingAddress',
    'VisitingCity',
    'VisitingCountry',
    'VisitingCountryCode',
    'VisitingZipCode',
    'WorkPlace',
    'WWW',
    'YourReference',
    'ZipCode',
  ];

  protected $writeable = [
    'Url',
    'Active',
    'Address1',
    'Address2',
    'Bank',
    'BankAccountNumber',
    'BG',
    'BIC',
    'BranchCode',
    'City',
    'ClearingNumber',
    'Comments',
    'CostCenter',
    'Country',
    'CountryCode',
    'Currency',
    'DisablePaymentFile',
    'Email',
    'Fax',
    'IBAN',
    'Name',
    'OrganisationNumber',
    'OurReference',
    'OurCustomerNumber',
    'PG',
    'Phone1',
    'Phone2',
    'PreDefinedAccount',
    'Project',
    'SupplierNumber',
    'TermsOfPayment',
    'VATNumber',
    'VATType',
    'VisitingAddress',
    'VisitingCity',
    'VisitingCountry',
    'VisitingCountryCode',
    'VisitingZipCode',
    'WorkPlace',
    'WWW',
    'YourReference',
    'ZipCode',
  ];

  protected $required = [
    'Name',
  ];

  /**
   * Override the REST path
   */
  protected $path = 'suppliers';


  /**
   * Retrieves a list of suppliers.
   *
   * @return array
   */
  public function all ()
  {
    return $this->sendRequest('GET');
  }


  /**
   * Retrieves a single supplier.
   *
   * @param $id
   * @return array
   */
  public function find ($id)
  {
    return $this->sendRequest('GET', $id);
  }


  /**
   * Creates a supplier.
   *
   * @param array   $params
   * @return array
   */
  public function create (array $params)
  {
    return $this->sendRequest('POST', null, 'Supplier', $params);
  }


  /**
   * Updates a supplier.
   *
   * @param array   $params
   * @return array
   */
  public function update ($id, array $params)
  {
    return $this->sendRequest('PUT', $id, 'Supplier', $params);
  }


  /**
   * Removes a supplier.
   */
  public function delete ($id)
  {
    throw new Exception('Not implemented');
  }

}