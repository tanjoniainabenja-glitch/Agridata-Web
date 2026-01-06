<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AgriData Madagascar</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">

<style>
:root{
  --primary-green:#28a745;
  --text-dark:#333;
  --text-light:#666;
  --border:#e0e0e0;
  --bg:#f8f9fa;
  --white:#fff;
  --shadow-light:rgba(0,0,0,.05);
  --shadow-dark:rgba(0,0,0,.15);
}
*{box-sizing:border-box}
body{
  margin:0;
  font-family:'Inter',sans-serif;
  background:var(--bg);
  color:var(--text-dark);
}
a{text-decoration:none;color:inherit;}

.site-header{
  background:var(--white);
  padding:1rem 20px;
  border-bottom:1px solid var(--border);
  margin-bottom:30px;
}
.header-container{
  max-width:1200px;
  margin:auto;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.logo-group{
  display:flex;
  align-items:center;
  gap:1rem;
}
.logo-img{width:70px;}
.logo-text{
  font-size:1.2rem;
  font-weight:700;
  color:var(--primary-green);
  display:flex;
  gap:.5rem;
  align-items:center;
}
.logo-text span{
  color:var(--text-dark);
  font-weight:400;
}
.main-nav{
  display:flex;
  gap:2rem;
}
.main-nav a{
  font-weight:500;
  padding-bottom:5px;
  border-bottom:3px solid transparent;
  transition:.3s;
}
.main-nav a.active,
.main-nav a:hover{
  color:var(--primary-green);
  border-bottom-color:var(--primary-green);
}
.btn-login{
  border:2px solid var(--primary-green);
  padding:.4rem 1rem;
  border-radius:50px;
  color:var(--primary-green);
}
.btn-login:hover{
  background:var(--primary-green);
  color:white;
}

.page-container{
  max-width:1200px;
  margin:auto;
  padding:0 20px 30px;
}
.page-title{text-align:center;margin-bottom:30px;}
.page-title h1{font-size:2.5em;}

.search-bar{
  display:flex;
  justify-content:center;
  margin-bottom:40px;
}
.search-bar input{
  width:100%;
  max-width:450px;
  padding:12px 20px;
  border-radius:25px 0 0 25px;
  border:1px solid #ccc;
}
.search-bar button{
  padding:12px 25px;
  border-radius:0 25px 25px 0;
  border:none;
  background:var(--primary-green);
  color:white;
  cursor:pointer;
}

#results{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
  gap:25px;
}

.card{
  position:relative;
  background:white;
  border-radius:8px;
  overflow:hidden;
  box-shadow:0 2px 8px var(--shadow-light);
  transition:.2s;
}
.card:hover{
  transform:translateY(-5px);
  box-shadow:0 4px 12px var(--shadow-dark);
}

.card-image{
  width:100%;
  height:200px;
  object-fit:cover;
}

.card-content{padding:15px;}
.card-content h3{margin:0 0 10px;}
.card-content p{font-size:.9em;color:var(--text-light);}

.card-actions{
  position:absolute;
  top:10px;
  right:10px;
  display:flex;
  gap:8px;
  z-index:2;
}
.card-actions i{
  background:white;
  padding:6px;
  border-radius:50%;
  cursor:pointer;
  box-shadow:0 2px 5px rgba(0,0,0,.2);
}
.card-actions i:hover{
  background:var(--primary-green);
  color:white;
}

.add-card{
  display:flex;
  justify-content:center;
  align-items:center;
  font-size:60px;
  color:var(--primary-green);
  border:2px dashed var(--primary-green);
  cursor:pointer;
}

.modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.6);
  z-index:999;
}
.modal-content{
  background:white;
  width:420px;
  margin:8% auto;
  padding:20px;
  border-radius:10px;
}
.modal-content input,
.modal-content textarea{
  width:100%;
  padding:10px;
  margin-bottom:10px;
}
.modal-actions{text-align:right;}
.modal-actions button{
  background:var(--primary-green);
  color:white;
  border:none;
  padding:10px 20px;
  border-radius:5px;
}
.close{float:right;font-size:22px;cursor:pointer;}
</style>
</head>

<body>

<header class="site-header">
  <div class="header-container">
    <div class="logo-group">
      <img src="logo.png" class="logo-img">
      <a class="logo-text"><i class="fa fa-leaf"></i>AgriData <span>Madagascar</span></a>
    </div>
    <nav class="main-nav">
      <a href="#">Accueil</a>
      <a href="#" class="active">Données</a>
      <a href="#">Cartes</a>
      <a href="#">API</a>
      <a href="#">Contact</a>
    </nav>
    <a class="btn-login"><i class="fa fa-user"></i> Connexion</a>
  </div>
</header>

<main class="page-container">
  <div class="page-title"><h1>Produits AgriData</h1></div>

  <div class="search-bar">
    <input id="searchBox" placeholder="riz, carotte..." oninput="loadData()">
    <button onclick="loadData()">Rechercher</button>
  </div>

  <div id="results"></div>
</main>

<!-- MODAL CRUD -->
<div class="modal" id="crudModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2 id="modalTitle"></h2>
    <input type="hidden" id="pid">
    <input id="pname" placeholder="Nom">
    <textarea id="pdesc" placeholder="Description"></textarea>
    <input id="pimg" placeholder="image/CIMG.jpg">
    <div class="modal-actions">
      <button onclick="saveProduct()">Enregistrer</button>
    </div>
  </div>
</div>

<script>
const API = "crud.php?endpoint=datas";
const BASE_IMAGE_URL = "http://localhost/Agridata/";

/* ===== IMAGE FIX ===== */
function fixImageUrl(url){
  if(!url) return "https://via.placeholder.com/300?text=Pas+d+image";
  url = url.replace("10.0.2.2","localhost");
  if(!url.startsWith("http")) return BASE_IMAGE_URL + url;
  return url;
}

/* ===== LOAD + SEARCH ===== */
function loadData(){
  const q = searchBox.value.trim();
  const url = q ? `${API}&q=${encodeURIComponent(q)}` : API;

  fetch(url)
    .then(r=>r.json())
    .then(data=>{
      results.innerHTML="";
      data.forEach(item=>{
        const c=document.createElement("div");
        c.className="card";
        c.innerHTML=`
          <div class="card-actions">
            <i class="fa fa-eye"
              onclick="viewItem('${escape(item.nom)}','${escape(item.description)}','${item.image_url||""}')"></i>
            <i class="fa fa-pen"
              onclick="editItem('${item.id}','${escape(item.nom)}','${escape(item.description)}','${item.image_url||""}')"></i>
            <i class="fa fa-trash"
              onclick="deleteItem('${item.id}')"></i>
          </div>

          <img class="card-image" src="${fixImageUrl(item.image_url)}">

          <div class="card-content">
            <h3>${item.nom}</h3>
            <p>${item.description||""}</p>
          </div>`;
        results.appendChild(c);
      });

      const add=document.createElement("div");
      add.className="card add-card";
      add.innerHTML="+";
      add.onclick=openCreate;
      results.appendChild(add);
    });
}

/* ===== CRUD ===== */
function openCreate(){
  modalTitle.innerText="Ajouter un produit";
  pid.value=pname.value=pdesc.value=pimg.value="";
  crudModal.style.display="block";
}

function editItem(id,n,d,i){
  modalTitle.innerText="Modifier le produit";
  pid.value=id;pname.value=n;pdesc.value=d;pimg.value=i;
  crudModal.style.display="block";
}

function viewItem(n,d,i){
  modalTitle.innerText="Détails";
  pname.value=n;pdesc.value=d;pimg.value=i;
  crudModal.style.display="block";
}

function saveProduct(){
  const id=pid.value;
  const data={nom:pname.value,description:pdesc.value,image_url:pimg.value};
  fetch(API+(id?"&id="+id:""),{
    method:id?"PUT":"POST",
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify(data)
  }).then(()=>{closeModal();loadData();});
}

function deleteItem(id){
  if(!confirm("Supprimer ?")) return;
  fetch(`${API}&id=${id}`,{method:"DELETE"}).then(loadData);
}

function closeModal(){crudModal.style.display="none";}
function escape(s){return s?s.replace(/'/g,"&#39;").replace(/"/g,"&quot;"):"";}

loadData();
</script>

</body>
</html>
