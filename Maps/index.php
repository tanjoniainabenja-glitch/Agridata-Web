<?php // index.php ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cartes Madagascar (PHP + MongoDB)</title>
  <style>
    * { box-sizing: border-box }
    body { margin: 0; font-family: system-ui, Arial, sans-serif }
    .layout { display: grid; grid-template-columns: 280px 1fr 340px; height: 100vh }
    aside, main, nav { border: 1px solid #bbb; padding: 12px; overflow: auto }
    main > header, nav > header { font-weight: 700; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 6px }
    .filters { display: grid; gap: 4px }
    .filters label { font-size: 12px; color: #555 }
    .filters select, .filters input { width: 100%; padding: 6px }
    .item { padding: 8px; border-bottom: 1px solid #eee; cursor: pointer }
    .item:hover { background: #f5f8ff }
    .badge { display: inline-block; background: #eef2ff; border: 1px solid #d7dcff; padding: 2px 6px; border-radius: 6px; font-size: 12px; margin-right: 6px }
    #detail img {
      width: 100%;
      height: auto;
      border: 1px solid #ddd;
      border-radius: 4px;
      background: #fafafa;
      transition: transform 0.2s ease-in-out;
      cursor: grab;
    }
    #count { font-size: 12px; color: #666; margin-bottom: 8px }
    .btn {
      display: flex;
      padding: 0.4rem 1.6rem;
      border-style: solid;
      border-width: 2px;
      border-radius: 50px;
      cursor: pointer;
      align-items: center;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: transform 0.2s ease-out, box-shadow 0.2s ease-out, background-color 0.2s ease-out, color 0.2s ease-out;
      background-color: var(--primary-green);
      color: var(--white);
      border-color: var(--primary-green);
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); 
    }
    .btn:active { transform: translateY(2px); box-shadow: none; }
    .btn-primary:hover {
      background-color: #218c3a;
      transform: translateY(-3px);
      box-shadow: 0 7px 20px rgba(40, 167, 69, 0.3);
    }
  </style>
</head>
<body>
  <div class="layout">
    <aside>
      <h3>Carte de Madagascar</h3>
      <p>Filtre par province, type et recherche.</p>
      <div class="filters">
        <div>
          <label>Province</label>
          <select id="province"></select>
        </div>
        <div>
          <label>Type</label>
          <select id="type"></select>
        </div>
        <div>
          <label>Recherche par nom</label>
          <input id="q" type="search" placeholder="ex: Vakinankaratra" />
          <p></p>
        </div>
        <a href="../" class="btn primary">Acceuil</a>
        <a href="../backphp/index.php" class="btn primary">Donnée</a>
      </div>
    </aside>

    <main>
      <header>Description</header>
      <div id="detail">Sélectionnez une carte dans la navigation →</div>
    </main>

    <nav>
      <header>Navigation</header>
      <div id="count"></div>
      <div id="liste"></div>
    </nav>
  </div>

  <script>
    const API = 'api_cartes.php';
    let data = [];

    const els = {
      province: document.getElementById('province'),
      type: document.getElementById('type'),
      q: document.getElementById('q'),
      liste: document.getElementById('liste'),
      detail: document.getElementById('detail'),
      count: document.getElementById('count')
    };

    function setOptions(select, items, firstLabel) {
      select.innerHTML = `<option value="">${firstLabel}</option>` + items.map(v => `<option>${v}</option>`).join('');
    }

    function uniques(field) {
      return [...new Set(data.map(x => x[field]))].sort((a,b)=>a.localeCompare(b,'fr'));
    }

    function currentFilters() {
      return {
        province: els.province.value,
        type: els.type.value,
        q: els.q.value.trim().toLowerCase()
      };
    }

    function applyFilters(list) {
      const f = currentFilters();
      return list.filter(x =>
        (!f.province || x.province === f.province) &&
        (!f.type || x.type === f.type) &&
        (!f.q || x.nom.toLowerCase().includes(f.q))
      );
    }

    function renderList() {
      const list = applyFilters(data);
      els.count.textContent = `${list.length} élément(s)`;
      els.liste.innerHTML = list.map((x, i) => `
        <div class="item" data-i="${i}">
          <div>${x.nom}</div>
          <div><span class="badge">${x.province}</span><span class="badge">${x.type}</span></div>
        </div>
      `).join('');

      els.liste.querySelectorAll('.item').forEach((el, idx) => {
        el.addEventListener('click', () => showDetail(applyFilters(data)[idx]));
      });

      if (list[0]) showDetail(list[0]);
      else els.detail.textContent = 'Aucun résultat pour ces filtres.';
    }

    let currentScale = 1;
    function showDetail(item) {
      if (!item) return;
      const src = item.urlImage || item.chemin_image;
      els.detail.innerHTML = `
        <h2 style="margin:0 0 8px 0">${item.nom}</h2>
        <p><b>Province:</b> ${item.province} &nbsp;&nbsp; <b>Type:</b> ${item.type}</p>
		<div style="margin-top:8px; text-align:center">
          <button onclick="zoomImg(1.2)">🔍 +</button>
          <button onclick="zoomImg(1/1.2)">🔍 −</button>
          <button onclick="resetZoom()">↺ Réinitialiser</button>
        </div>
        <div style="text-align:center">
          <img id="zoomable" src="${src}" alt="${item.nom}"
               onerror="this.replaceWith(document.createTextNode('Image introuvable: ${src}'));">
        </div>
        
      `;
      currentScale = 1;

      // Activer zoom avec la molette
      const img = document.getElementById('zoomable');
      img.addEventListener('wheel', e => {
        e.preventDefault();
        zoomImg(e.deltaY < 0 ? 1.1 : 1/1.1);
      });
    }

    function zoomImg(factor) {
      currentScale *= factor;
      document.getElementById('zoomable').style.transform = `scale(${currentScale})`;
    }

    function resetZoom() {
      currentScale = 1;
      document.getElementById('zoomable').style.transform = 'scale(1)';
    }

    async function load() {
      try {
        const res  = await fetch(API, { cache: 'no-store' });
        const text = await res.text();
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${text}`);
        let json;
        try { json = JSON.parse(text); }
        catch { throw new Error('Réponse non-JSON: ' + text.slice(0, 300)); }
        data = json;

        setOptions(els.province, uniques('province'), 'Toutes les provinces');
        setOptions(els.type, uniques('type'), 'Tous les types');
        els.province.addEventListener('change', renderList);
        els.type.addEventListener('change', renderList);
        els.q.addEventListener('input', renderList);
        renderList();
      } catch (err) {
        console.error(err);
        els.detail.textContent = 'Erreur API: ' + err.message;
      }
    }
    load();
  </script>
</body>
</html>
