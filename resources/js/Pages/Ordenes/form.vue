<template>
  <div>
    <v-card>
      <v-card-title>{{formTitle}}</v-card-title>
      <v-card-text>
<!--      <form @submit.stop.prevent="enviar">-->
<!--        <b-alert dismissible :show="errors.length">-->
<!--          {{ errors }}-->
<!--        </b-alert>-->
        <template v-for="(item,key) in form">
          <template v-if="['text','password','date','textarea','select','search'].includes(item.type)">
            <v-text-field
                v-model="item.value"
                :id="key"
                v-if="['text','password','date'].includes(item.type)"
                outlined
                dense
                hide-details="auto"
                :type="item.type"
                :label="item.label"
                :error="item.state"
                :error-messages="item.stateText"
                class="my-2"
            ></v-text-field>
            <v-textarea
                v-if="item.type==='textarea'"
                :id="key"
                v-model="item.value"
                rows="2"
                outlined
                dense
                hide-details="auto"
                :label="item.label"
                :error="item.state"
                :error-messages="item.stateText"
                class="my-2"
            ></v-textarea>
<!--            <vue-bootstrap-typeahead
                size="sm"
                :placeholder="item.label"
                :data="options"
                v-model="responsableValue"
                v-if="item.type==='search'"
                :serializer="s => s.nombreResponsable"
                @hit="cliente = $event"
                ref="typeahead"
                backgroundVariant="light"
            >
            </vue-bootstrap-typeahead>-->
            <v-autocomplete
                v-if="item.type==='search'"
                v-model="responsableValue"
                :loading="loading"
                :items="items"
                :search-input.sync="search"
                :search-input.prop="selectSeachDemo(responsableValue)"
                hide-no-data
                hide-selected
                item-text="nombreResponsable"
                label="nombreResposanble"
                return-object
                dense
                outlined
                hide-details="auto"
                class="my-2"
            ></v-autocomplete>
          </template>
          <v-checkbox
              v-if="item.type==='boolean'"
              v-model="item.value"
              :id="key"
              :state="item.state"
          >{{ item.label }}
          </v-checkbox>
        </template>
<!--        <div class="table-responsive">-->
<!--          <b-table-simple class="table-hover table-small texto-small">-->
<!--            <b-thead>-->
<!--              <b-tr>-->
<!--                <b-th>Formato</b-th>-->
<!--                <b-th>Dimension</b-th>-->
<!--                <b-th>Cant.</b-th>-->
<!--                <b-th></b-th>-->
<!--              </b-tr>-->
<!--            </b-thead>-->
<!--            <b-tbody>-->
<!--              <template v-for="(product,key) in productos">-->
<!--                <b-tr>-->
<!--                  <b-td>{{ product.formato }}</b-td>-->
<!--                  <b-td>{{ product.dimension }}</b-td>-->
<!--                  <b-td>{{ product.cantidad }}</b-td>-->
<!--                  <b-td>-->
<!--                    &lt;!&ndash;                                        <b-form-spinbutton id="demo-sb" v-model="productosSell[key].cantidad" min="0"&ndash;&gt;-->
<!--                    &lt;!&ndash;                                                           max="100" size="sm" inline></b-form-spinbutton>&ndash;&gt;-->
<!--                    <b-input type="number" id="demo-sb" v-model="productosSell[key].cantidad"-->
<!--                             size="sm" min="0"></b-input>-->
<!--                  </b-td>-->
<!--                </b-tr>-->
<!--              </template>-->
<!--            </b-tbody>-->
<!--          </b-table-simple>-->
<!--        </div>-->
<!--      </form>-->
<!--      <template #modal-footer="{ ok, cancel }">-->
<!--        <b-button variant="danger" @click="cancel()" :size="'sm'">-->
<!--          Cancel-->
<!--        </b-button>-->
<!--        <loading-button :loading="sending" :variant="'primary'" :size="'sm'"-->
<!--                        @click.native="ok()" :text="'Guardar'" :textLoad="'Guardando'">Guardar-->
<!--        </loading-button>-->
<!--      </template>-->
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Orden",
  components: {
    // VueBootstrapTypeahead,
  },
  props: {
    isNew: Boolean,
    isVenta: Boolean,
    title: String,
    id: String,
    itemRow: Object,
    productos: Array,
    productosSell: Array,
    tipo: Number,
  },
  computed: {
    formTitle() {
      return `${this.isNew ? this.titulo1 : this.titulo2} ${this.title}`;
    },
  },
  data() {
    return {
      sending: false,
      titulo1: "Nuevo - ",
      titulo2: "Modificar - ",
      form: {
        responsable: {
          label: 'Cliente',
          value: "",
          type: "search",
          state: null,
          stateText: null
        },
        telefono: {
          label: 'Telefono',
          value: "",
          type: "text",
          state: null,
          stateText: null
        },
        observaciones: {
          label: 'Observaciones',
          value: "",
          type: "textarea",
          state: null,
          stateText: null
        }
      },
      idForm: -1,
      errors: Array,
      options: [],
      responsableValue: "",
      cliente: "",
      idCliente: null,
      loading: false,
      items: [],
      search: null,
    }
  },
  methods: {
    reset: function () {
      this.limpiar();
      if (this.isNew) {
        this.idForm = null;
        for (let key in this.form) {
          this.form[key].value = "";
        }
        this.responsableValue = ""
      } else {
        if ('id' in this.itemRow) {
          this.idForm = this.itemRow['id'];
        }
        for (let key in this.form) {
          if (['central', 'enable'].includes(key)) {
            this.form[key].value = (this.itemRow[key] === 1)
          } else if (['responsable'].includes(key)) {
            this.responsableValue = this.itemRow[key];
          } else {
            this.form[key].value = this.itemRow[key];
          }
        }
        for (let key in this.productosSell) {
          for (let value of this.itemRow.detallesOrden) {
            if (this.productosSell[key].id === value.stock) {
              this.productosSell[key].cantidad = value.cantidad;
            }
          }
        }
      }
    },
    limpiar() {
      for (let key in this.form) {
        this.form[key].state = null;
        this.form[key].stateText = null;
      }
      this.errors = [];
    },
    enviar() {
      this.sending = true;
      this.limpiar();
      let producto = new FormData();
      producto.append('tipo', this.tipo);
      if (this.idForm) {
        producto.append('id', this.idForm);
      }
      if (this.responsableValue) {
        this.form.responsable.value = this.responsableValue;
      }
      for (let key in this.form) {
        producto.append(key, this.form[key].value);
      }
      if (this.idCliente) {
        producto.append('cliente', this.idCliente);
      }
      let items = [];
      for (let value of this.productosSell) {
        if (value.cantidad > 0) {
          items = [...items, value];
        }
      }
      if (items.length > 0) {
        producto.append('productos', JSON.stringify(items));
      }
      axios.post('/orden', producto, {headers: {'Content-Type': 'multipart/form-data'}})
          .then(({data}) => {
            if (data["status"] == 0) {
              this.$bvModal.hide(this.id)
              this.$inertia.get(data["path"])
            } else {
              for (let key in this.form) {
                if (key in data.errors) {
                  this.form[key].state = false;
                  this.form[key].stateText = data.errors[key][0];
                } else {
                  this.form[key].state = true;
                  this.form[key].stateText = "";
                }
              }
            }
          })
          .catch(error => {
            // handle error
            this.errors = error
            console.log(error);
          }).finally(() => {
        this.sending = false;
      })
    },
    selectSeachDemo(item) {
      this.form.responsable.value = item?.nombreResponsable;
      this.form.telefono.value = item?.telefono;
      this.idCliente = item?.id
    },
    querySelections (v) {
      // Items have already been requested
      if (this.isLoading) return
      this.loading = true
      // Simulated ajax query
      axios.get('/search/' + escape(v)).then(({data}) => {
        this.items = data.items;
      })
      .finally(()=>{
        this.loading = false
      });
    }
  },
  watch: {
    search (val) {
      val && val !== this.responsableValue && this.querySelections(val)
    },
  }
}
</script>
<style>
.texto-small {
  font-size: 0.85em;
}
</style>
