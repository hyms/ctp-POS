<template>
  <!--  <div class="main-content">-->
  <!--    <breadcumb :page="$t('Create_Permission')" :folder="$t('Settings')"/>-->

  <!--    <validation-observer ref="Create_Permission">-->
  <!--      <b-form @submit.prevent="Submit_Permission">-->
  <!--        <b-row>-->
  <!--          <b-col lg="12" md="12" sm="12">-->
  <!--            <b-card>-->
  <!--              <b-row>-->
  <!--                &lt;!&ndash; Role Name &ndash;&gt;-->
  <!--                <b-col md="6">-->
  <!--                  <validation-provider-->
  <!--                    name="Role name"-->
  <!--                    :rules="{ required: true}"-->
  <!--                    v-slot="validationContext"-->
  <!--                  >-->
  <!--                    <b-form-group :label="$t('RoleName') + ' ' + '*'">-->
  <!--                      <b-form-input-->
  <!--                        :placeholder="$t('Enter_Role_Name')"-->
  <!--                        :state="getValidationState(validationContext)"-->
  <!--                        aria-describedby="Role-feedback"-->
  <!--                        v-model="role.name"-->
  <!--                      ></b-form-input>-->
  <!--                      <b-form-invalid-feedback id="Role-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
  <!--                    </b-form-group>-->
  <!--                  </validation-provider>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Role Description &ndash;&gt;-->
  <!--                <b-col md="6">-->
  <!--                  <b-form-group :label="$t('RoleDescription')">-->
  <!--                    <b-form-input-->
  <!--                      :placeholder="$t('Enter_Role_Description')"-->
  <!--                      v-model="role.description"-->
  <!--                    ></b-form-input>-->
  <!--                  </b-form-group>-->
  <!--                </b-col>-->
  <!--              </b-row>-->

  <!--              <b-row class="mt-4">-->
  <!--                &lt;!&ndash;dashboard &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-dashboard-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('dashboard')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-dashboard "-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-dashboard"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;dashboard &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="dashboard"-->
  <!--                                >-->
  <!--                                <span>{{$t('dashboard')}} <i v-b-tooltip.hover.bottom title="if unchecked only welcome message will be displayed in dashboard" class="text-info text-15 font-weight-bold i-Speach-BubbleAsking"></i></span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash;Users &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-UserManagement-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('UserManagement')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-UserManagement "-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion1"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Users View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="users_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Users ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="users_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Users Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="users_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Users Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="users_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Users record view &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="record_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('ShowAll')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash;  Permissions &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Permissions-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('UserPermissions')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Permissions "-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion2"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Permissions View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="permissions_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Permissions ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="permissions_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Permissions Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="permissions_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Permissions Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="permissions_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash;  Products &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Products-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Products')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Products"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion3"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Products View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="products_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Products ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="products_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Products Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="products_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Products Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="products_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Products Barcode &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="barcode_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('Barcode')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Products Import &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="product_import"-->
  <!--                                >-->
  <!--                                <span>{{$t('import_products')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                             &lt;!&ndash;Category &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="category"-->
  <!--                                >-->
  <!--                                <span>{{$t('Categories')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Brand  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input type="checkbox" checked v-model="permissions" value="brand">-->
  <!--                                <span>{{$t('Brand')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                             &lt;!&ndash;Unit  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input type="checkbox" checked v-model="permissions" value="unit">-->
  <!--                                <span>{{$t('Units')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash;  Adjustment &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Adjustment-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('StockAdjustement')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Adjustment"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion4"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Adjustment View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="adjustment_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Adjustment ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="adjustment_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Adjustment Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="adjustment_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Adjustment Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="adjustment_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash;  Transfer &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Transfer-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('StockTransfers')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Transfer"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion5"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Transfer View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="transfer_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Transfer ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="transfer_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Transfer Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="transfer_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Transfer Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="transfer_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash;  Expense &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Expense-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Expenses')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Expense"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion6"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Expense View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="expense_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Expense ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="expense_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Expense Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="expense_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Expense Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="expense_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Sales &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Sales-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Sales')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Sales"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion7"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Sales View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sales_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Sales ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sales_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Sales Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sales_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Sales Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sales_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Sales POS &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Pos_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('pointofsales')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;shipment &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="shipment"-->
  <!--                                >-->
  <!--                                <span>{{$t('Shipments')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Purchases &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Purchases-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Purchases')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Purchases"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion8"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Purchases View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchases_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Purchases ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchases_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Purchases Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchases_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Purchases Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchases_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Quotations &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Quotations-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Quotations')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Quotations"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion9"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Quotations View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Quotations_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Quotations ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Quotations_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Quotations Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Quotations_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Quotations Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Quotations_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Sale Returns &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Return-Sale-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('SalesReturn')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Return-Sale"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion10"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Return Sale View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sale_Returns_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Return Sale ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sale_Returns_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Return Sale Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sale_Returns_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Return Sale Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Sale_Returns_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Purchase Return &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Return-Purchase-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('PurchasesReturn')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Return-Purchase"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion11"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Return Purchase View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchase_Returns_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Return Purchase ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchase_Returns_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Return Purchase Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchase_Returns_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Return Purchase Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Purchase_Returns_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Payment Sales &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Payment-Sales-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('PaymentsSales')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Payment-Sales"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion12"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Payment Sales View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_sales_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Sales ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_sales_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Sales Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_sales_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Sales Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_sales_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Payment Purchases &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Payment-Purchases-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('PaymentsPurchases')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Payment-Purchases"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion13"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Payment Purchases View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_purchases_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Purchases ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_purchases_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Purchases Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_purchases_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Purchases Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_purchases_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Payment Returns &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Payment-Returns-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('PaymentsReturns')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Payment-Returns"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion14"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Payment Returns View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_returns_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Returns ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_returns_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Returns Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_returns_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Payment Returns Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_returns_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Customers &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Customers-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Customers')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Customers"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion15"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Customers View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Customers_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Customers ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Customers_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Customers Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Customers_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Customers Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Customers_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Customers Import &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="customers_import"-->
  <!--                                >-->
  <!--                                <span>{{$t('Import_Customers')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;pay_all_sell_due_at_a_time &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="pay_due"-->
  <!--                                >-->
  <!--                                <span>{{$t('pay_all_sell_due_at_a_time')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;pay_sale_return_due &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="pay_sale_return_due"-->
  <!--                                >-->
  <!--                                <span>{{$t('pay_all_sell_return_due_at_a_time')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Suppliers &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Suppliers-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Suppliers')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Suppliers"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion16"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Suppliers View &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Suppliers_view"-->
  <!--                                >-->
  <!--                                <span>{{$t('View')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Suppliers ADD &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Suppliers_add"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Suppliers Edit &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Suppliers_edit"-->
  <!--                                >-->
  <!--                                <span>{{$t('Edit')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Suppliers Delete &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Suppliers_delete"-->
  <!--                                >-->
  <!--                                <span>{{$t('Del')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Suppliers Import &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Suppliers_import"-->
  <!--                                >-->
  <!--                                <span>{{$t('Import_Suppliers')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;pay_all_purchase_due_at_a_time &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="pay_supplier_due"-->
  <!--                                >-->
  <!--                                <span>{{$t('pay_all_purchase_due_at_a_time')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;pay_all_purchase_return_due_at_a_time &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="pay_purchase_return_due"-->
  <!--                                >-->
  <!--                                <span>{{$t('pay_all_purchase_return_due_at_a_time')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Reports &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Reports-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Reports')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Reports"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion17"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Reports_payments_Sales  &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_payments_Sales"-->
  <!--                                >-->
  <!--                                <span>{{$t('Reports_payments_Sales')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Reports_payments_Purchases  &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_payments_Purchases"-->
  <!--                                >-->
  <!--                                <span>{{$t('Reports_payments_Purchases')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Reports_payments_Sale_Returns  &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_payments_Sale_Returns"-->
  <!--                                >-->
  <!--                                <span>{{$t('Reports_payments_Sale_Return')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Reports_payments_purchase_Return  &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_payments_purchase_Return"-->
  <!--                                >-->
  <!--                                <span>{{$t('Reports_payments_Purchase_Return')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash; Sales Reports &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_sales"-->
  <!--                                >-->
  <!--                                <span>{{$t('SalesReport')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Purchases Reports &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_purchase"-->
  <!--                                >-->
  <!--                                <span>{{$t('PurchasesReport')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash; Customers Reports &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_customers"-->
  <!--                                >-->
  <!--                                <span>{{$t('CustomersReport')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Suppliers Reports &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_suppliers"-->
  <!--                                >-->
  <!--                                <span>{{$t('SuppliersReport')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Proft and Loss &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_profit"-->
  <!--                                >-->
  <!--                                <span>{{$t('ProfitandLoss')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Product Quantity Alerts &ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Reports_quantity_alerts"-->
  <!--                                >-->
  <!--                                <span>{{$t('ProductQuantityAlerts')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Warehouse Stock Chart&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Warehouse_report"-->
  <!--                                >-->
  <!--                                <span>{{$t('WarehouseStockChart')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Top_Selling_Products&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Top_products"-->
  <!--                                >-->
  <!--                                <span>{{$t('Top_Selling_Products')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;Top_customers&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="Top_customers"-->
  <!--                                >-->
  <!--                                <span>{{$t('Top_customers')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;users_report&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="users_report"-->
  <!--                                >-->
  <!--                                <span>{{$t('Users_Report')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;stock_report&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="stock_report"-->
  <!--                                >-->
  <!--                                <span>{{$t('stock_report')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;product_report&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="product_report"-->
  <!--                                >-->
  <!--                                <span>{{$t('product_report')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;product_sales_report&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="product_sales_report"-->
  <!--                                >-->
  <!--                                <span>{{$t('product_sales_report')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;product_purchases_report&ndash;&gt;-->
  <!--                            <b-col md="12">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="product_purchases_report"-->
  <!--                                >-->
  <!--                                <span>{{$t('Product_purchases_report')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                 &lt;!&ndash; hrm &ndash;&gt;-->
  <!--                <b-col md="4">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-hrm-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('HRM')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-hrm"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion21"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                             &lt;!&ndash;view Employee&ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="view_employee"-->
  <!--                                >-->
  <!--                                <span>{{$t('view_employee')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;add Employee&ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="add_employee"-->
  <!--                                >-->
  <!--                                <span>{{$t('Add_Employee')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;edit employee &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="edit_employee"-->
  <!--                                >-->
  <!--                                <span>{{$t('edit_employee')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                             &lt;!&ndash;delete employee &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="delete_employee"-->
  <!--                                >-->
  <!--                                <span>{{$t('delete_employee')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Company &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="company"-->
  <!--                                >-->
  <!--                                <span>{{$t('Company')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;department &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="department"-->
  <!--                                >-->
  <!--                                <span>{{$t('department')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;designation &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="designation"-->
  <!--                                >-->
  <!--                                <span>{{$t('Designation')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;office_shift &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="office_shift"-->
  <!--                                >-->
  <!--                                <span>{{$t('Office_Shift')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;attendance &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="attendance"-->
  <!--                                >-->
  <!--                                <span>{{$t('Attendance')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;leave &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="leave"-->
  <!--                                >-->
  <!--                                <span>{{$t('Leave_request')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;holiday &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="holiday"-->
  <!--                                >-->
  <!--                                <span>{{$t('Holiday')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          -->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->

  <!--                &lt;!&ndash; Settings &ndash;&gt;-->
  <!--                <b-col md="6">-->
  <!--                  <b-card no-body class="ul-card__border-radius">-->
  <!--                    <b-card-header header-tag="header" class="p-1" role="tab">-->
  <!--                      <b-button-->
  <!--                        class="card-title mb-0"-->
  <!--                        block-->
  <!--                        href="#"-->
  <!--                        v-b-toggle.panel-Settings-->
  <!--                        variant="transparent"-->
  <!--                      >{{$t('Settings')}}</b-button>-->
  <!--                    </b-card-header>-->
  <!--                    <b-collapse-->
  <!--                      id="panel-Settings"-->
  <!--                      :visible="true"-->
  <!--                      accordion="my-accordion18"-->
  <!--                      role="tabpanel"-->
  <!--                    >-->
  <!--                      <b-card-body>-->
  <!--                        <b-card-text>-->
  <!--                          <b-row>-->
  <!--                            &lt;!&ndash;Settings System &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="setting_system"-->
  <!--                                >-->
  <!--                                <span>{{$t('SystemSettings')}} + {{$t('update_settings')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;sms_settings  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="sms_settings"-->
  <!--                                >-->
  <!--                                <span>{{$t('sms_settings')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;pos_settings  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="pos_settings"-->
  <!--                                >-->
  <!--                                <span>{{$t('pos_settings')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;payment_gateway  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="payment_gateway"-->
  <!--                                >-->
  <!--                                <span>{{$t('payment_gateway')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                             &lt;!&ndash;mail_settings  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="mail_settings"-->
  <!--                                >-->
  <!--                                <span>{{$t('mail_settings')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->

  <!--                            &lt;!&ndash;Currency  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="currency"-->
  <!--                                >-->
  <!--                                <span>{{$t('Currencies')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Warehouse  &ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input-->
  <!--                                  type="checkbox"-->
  <!--                                  checked-->
  <!--                                  v-model="permissions"-->
  <!--                                  value="warehouse"-->
  <!--                                >-->
  <!--                                <span>{{$t('Warehouses')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                            &lt;!&ndash;Backup&ndash;&gt;-->
  <!--                            <b-col md="6">-->
  <!--                              <label class="checkbox checkbox-outline-primary">-->
  <!--                                <input type="checkbox" checked v-model="permissions" value="backup">-->
  <!--                                <span>{{$t('Backup')}}</span>-->
  <!--                                <span class="checkmark"></span>-->
  <!--                              </label>-->
  <!--                            </b-col>-->
  <!--                          </b-row>-->
  <!--                        </b-card-text>-->
  <!--                      </b-card-body>-->
  <!--                    </b-collapse>-->
  <!--                  </b-card>-->
  <!--                </b-col>-->
  <!--              </b-row>-->
  <!--              &lt;!&ndash; End row &ndash;&gt;-->
  <!--              -->
  <!--               <b-col md="12">-->
  <!--                <b-button variant="primary" type="submit"  :disabled="SubmitProcessing">{{$t('submit')}}</b-button>-->
  <!--                  <div v-once class="typo__p" v-if="SubmitProcessing">-->
  <!--                    <div class="spinner sm spinner-primary mt-3"></div>-->
  <!--                  </div>-->
  <!--                </b-col>-->

  <!--            </b-card>-->
  <!--          </b-col>-->
  <!--        </b-row>-->
  <!--      </b-form>-->
  <!--    </validation-observer>-->
  <!--  </div>-->
</template>

<script>
// import NProgress from "nprogress";
//
// export default {
//   metaInfo: {
//     title: "Create Permissions"
//   },
//   data() {
//     return {
//       SubmitProcessing:false,
//       isLoading: true,
//       permissions: [],
//       role: {
//         name: "",
//         description: ""
//       }
//     };
//   },
//
//   methods: {
//     //------------- Submit Validation Create Permissions
//     Submit_Permission() {
//       this.$refs.Create_Permission.validate().then(success => {
//         if (!success) {
//           this.makeToast(
//             "danger",
//             this.$t("Please_fill_the_form_correctly"),
//             this.$t("Failed")
//           );
//         } else {
//           this.Create_Permission();
//         }
//       });
//     },
//
//     //---Validate State Fields
//     getValidationState({ dirty, validated, valid = null }) {
//       return dirty || validated ? valid : null;
//     },
//
//     //------ Toast
//     makeToast(variant, msg, title) {
//       this.$root.$bvToast.toast(msg, {
//         title: title,
//         variant: variant,
//         solid: true
//       });
//     },
//
//
//     //------------------------ Create Permissions -------------------\\
//     Create_Permission() {
//       this.SubmitProcessing = true;
//       NProgress.start();
//       NProgress.set(0.1);
//       axios
//         .post("roles", {
//           role: this.role,
//           permissions: this.permissions
//         })
//         .then(response => {
//           this.SubmitProcessing = false;
//           NProgress.done();
//           this.makeToast(
//             "success",
//             this.$t("Create.TitleRole"),
//             this.$t("Success")
//           );
//
//           this.$router.push({
//             name: "groupPermission"
//           });
//         })
//         .catch(error => {
//           this.SubmitProcessing = false;
//           NProgress.done();
//           this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
//         });
//     }
//   }, //end Methods
//
//   created: function() {
//   }
// };
</script>
