<title>Controle Básico de Acervo</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <div id="app">
        <h1 class="text-center fs-1">Controle Básico de Acervo</h1>
        <create-collection></create-collection>
        <collection-list></collection-list>
      </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>


<script>
// Component CollectionList
const CollectionList = {
  template: `
    <div class="container mt-4">
      <div>
        <form @submit.prevent="searchCollection">
          <input type="text" v-model="searchTerm" placeholder="Buscar...">
          <select v-model="searchOption">
          <option value="">Selecione uma opção de busca</option>
          <option value="title">Título</option>
          <option value="description">Descrição</option>
          <option value="materialType">Tipo de Material</option>
          <option value="authorCollection">Autor da Coleção</option>
          <option value="locationCollection">Local da Coleção</option>
          </select>
          <button class="pl-4 btn btn-sm text-color red" @click="reloadPage()" type="submit">✘</button>
          <button class="pl-4 btn btn-sm btn-primary rounded-circle" @click="searchCollection()" type="submit">🔎</button>
        </form>
      </div>
      <div v-if="collections.length === 0">Nenhum registro encontrado...</div>
      <table class="table" v-else>
        <thead>
          <tr>
            <th scope="col">Título</th>
            <th scope="col">Descrição</th>
            <th scope="col">Tipo de Material</th>
            <th scope="col">Autor da Coleção</th>
            <th scope="col">Localização da Coleção</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="collection in collections" :key="collection.id">
            <td>{{ collection.title }}</td>
            <td>{{ collection.description }}</td>
            <td>{{ collection.material_type }}</td>
            <td>{{ collection.author_collection }}</td>
            <td>{{ collection.location_collection }}</td>
            <td>
                <button @click="deleteCollection(collection.id)" class="btn btn-sm btn-danger rounded-circle">✘</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  data() {
    return {
      collections: [],
      searchOption:"",
      searchTerm:""
    };
  },
  mounted() {
    fetch('/wp-json/collection-book/v1/collections',{
      method: 'GET', 
      headers: {
        'Content-Type': 'application/json',
        'X-User-Capability': 'manage_options'
      }
    })
    .then(response => response.json())
    .then(data => {
      //console.log(data)
      this.collections = data;
    })
    .catch(error => {
      console.error('Erro ao carregar as coleções:', error);
    });
  },
  methods: {
    searchCollection(){
        fetch(`/wp-json/collection-book/v1/collections?${this.searchOption}=${this.searchTerm}`, {method: 'GET', credentials: 'same-origin'})
        .then(response => response.json())
        .then(data => {
          //console.log(data)
          this.collections = data;
        })
        .catch(error => {
          console.error('Erro ao carregar as coleções:', error);
        });
    },
    deleteCollection(collectionId) {
        // Delete collection ID
        fetch(`/wp-json/collection-book/v1/collections/${collectionId}`, {
            method: 'DELETE',
            headers: {
            'Content-Type': 'application/json'
            // Se necessário, adicione headers de autenticação ou outros headers requeridos pelo seu servidor
            },
        })
        .then(response => {
            if (!response.ok) {
            throw new Error('Erro ao excluir a coleção');
            }
            // Remover a coleção deletada da lista localmente
            this.collections = this.collections.filter(collection => collection.id !== collectionId);
        })
        .catch(error => {
            console.error('Erro ao excluir a coleção:', error);
        });
    },
    reloadPage(){
      this.searchTerm = ""
      this.searchOption = ""
      this.searchCollection()
    }
  }
};

// Component CreateCollection
const CreateCollection = {
    template:`
    <button type="button" class="btn btn-primary float-end mr-4" @click="showModal = true">Nova Obra</button>
    <!-- Modal Create -->
    <div id="createCollectionModal" class="modal show mt-5" tabindex="-1" role="dialog" v-if="showModal" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Obra</h5>
                <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário para criar uma nova Obra -->
                <form @submit.prevent="createCollection">
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" v-model="newCollection.title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" id="description" v-model="newCollection.description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="material_type" class="form-label">Tipo de Material</label>
                    <input type="text" class="form-control" id="material_type" v-model="newCollection.material_type">
                </div>
                <div class="mb-3">
                    <label for="author_collection" class="form-label">Autor da Obra</label>
                    <input type="text" class="form-control" id="author_collection" v-model="newCollection.author_collection">
                </div>
                <div class="mb-3">
                    <label for="location_collection" class="form-label">Localização da Obra</label>
                    <input type="text" class="form-control" id="location_collection" v-model="newCollection.location_collection">
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
            </div>
        </div>
    </div>
    `,
    data() {
        return {
            showModal: false,
            newCollection: {
                title: '',
                description: '',
                material_type: '',
                author_collection: '',
                location_collection: ''
            }
        };
    },
    methods: {       
        createCollection() {
            // create Collection
            fetch('/wp-json/collection-book/v1/collections', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.newCollection)
            })
            .then(response => {
                if (!response.ok) {
                throw new Error('Erro ao criar a coleção');
                }
                // Close modal 
                this.showModal = false
                this.newCollection = {}
                this.location.reload();

            })
            .catch(error => {
                console.error('Erro ao criar a coleção:', error);
            });
        }
    }
};


// Start Vue 
const app = Vue.createApp({
  components: {
    'collection-list': CollectionList,
    'create-collection': CreateCollection
  }
});

// Mount Vue
app.mount('#app');
</script>

