<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniorangeTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mo_config', function (Blueprint $table) {
            $table->string('id', 10)->unique()->nullable();
            $table->string('mo_saml_host_name', 100)->nullable();
            $table->string('saml_identity_name', 100)->nullable();
            $table->string('idp_entity_id', 100)->nullable();
            $table->string('saml_login_url', 100)->nullable();
            $table->string('saml_login_binding_type', 100)->nullable();
            $table->string('saml_logout_url', 100)->nullable();
            $table->string('force_authentication', 100)->nullable();
            $table->string('sp_base_url', 100)->nullable();
            $table->string('sp_entity_id', 100)->nullable();
            $table->string('acs_url', 100)->nullable();
            $table->string('single_logout_url', 100)->nullable();
            $table->string('saml_am_email', 100)->default('NameID')->nullable();
            $table->string('saml_am_username', 100)->default('NameID')->nullable();
            $table->string('mo_saml_custom_attrs_mapping', 100)->nullable();
            $table->string('force_sso', 100)->nullable();
            $table->string('relaystate_url', 100)->nullable();
            $table->string('site_logout_url', 100)->nullable();
            $table->string('saml_x509_certificate', 1500)->nullable();
            $table->string('mo_saml_message', 300)->nullable();
            $table->string('mo_saml_admin_phone', 20)->nullable();
            $table->string('mo_saml_verify_customer', 10)->nullable();
            $table->string('mo_saml_idp_config_complete', 100)->nullable();
        });
        Schema::create('mo_admin', function (Blueprint $table) {
            $table->string('id', 10)->unique()->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password', 100)->nullable();
        });
        Schema::create('mo_customer_details', function (Blueprint $table) {
            $table->string('id', 10)->unique()->nullable();
            $table->string('mo_saml_admin_email', 100)->nullable();
            $table->string('mo_saml_admin_customer_key', 100)->nullable();
            $table->string('mo_saml_admin_api_key', 100)->nullable();
            $table->string('mo_saml_customer_token', 100)->nullable();
            $table->string('mo_saml_registration_status', 100)->nullable();
        });
        $tables = [
            'mo_config',
            'mo_admin',
            'mo_customer_details'
        ];
        foreach ($tables as $table) {
            DB::statement('ALTER TABLE ' . $table . ' ENGINE = InnoDB');
        }
        DB::statement("INSERT INTO mo_config(id,mo_saml_host_name,saml_am_email,saml_am_username) VALUES('1','https://auth.miniorange.com/','NameID','NameID')");
        DB::insert("INSERT INTO mo_admin(id) VALUES('1')");
        DB::insert("INSERT INTO mo_customer_details(id) VALUES('1')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mo_config');
        Schema::dropIfExists('mo_admin');
        Schema::dropIfExists(('mo_customer_details'));
    }
}