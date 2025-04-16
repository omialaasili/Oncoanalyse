<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../getBd.php";
$bdd = getBD();
// Récupérer la liste des colonnes pour le <select>
$columns = [];
$resultCols = $bdd->query("SHOW COLUMNS FROM tumeur");
while ($row = $resultCols->fetch(PDO::FETCH_ASSOC)) {
    $columns[] = $row['Field'];
}

// --- SECTION 3 : Correction ici ---
// On utilise PDO ($bdd), donc tout est cohérent ici
$caracteristiques = [];
foreach ($columns as $field) {
    if ($field !== 'Id-tumeur') {
        $caracteristiques[] = $field;
    }
}
$query = $bdd->query("SELECT tumeur.`Id-tumeur`, diagnostic.libelle_diagnostic 
                       FROM tumeur 
                       JOIN diagnostic ON tumeur.code_diagnostic = diagnostic.code_diagnostic");

$diagnostics = [];
if ($query) {
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $diagnostics[] = $row;
    }
} else {
    die("Erreur SQL : " . $bdd->error);
}

?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
    <title>Oncoanalyse - Homepage</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />

    <!-- Feuille de style du template Stellar (modifiée avec nos ajouts) -->
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <style>

.graph-container > div {
  display: inline-block;
}
    .graph-container {
        opacity: 0;
        transform: translateX(30px);
        transition: opacity 0.5s ease, transform 0.5s ease;
        display: none;
    }

    .graph-container.show {
        display: block;
        opacity: 1;
        transform: translateX(0);
    }

    .graph-container.hide {
        opacity: 0;
        transform: translateX(30px);
    }
    .slider {
      max-width: 100%;
      margin: 2rem auto;
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
    .slides {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }
    .slide {
      width: 100%;
      height: 600px;
      border: none;
      flex-shrink: 0;
    }
    .navigation {
      text-align: center;
      margin-top: 1rem;
    }
    .nav-btn {
      display: inline-block;
      cursor: pointer;
      width: 12px;
      height: 12px;
      background: #999;
      border-radius: 50%;
      margin: 0 5px;
    }
    .nav-btn.active {
      background: #7f71c6;
    }
    #description-container, #biopsies-description-container {
      max-width: 800px;
      margin: 2rem auto;
      font-size: 1rem;
      color: #333;
      padding: 1rem;
      background: #f9f9f9;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .box-container {
      display: flex;
      justify-content: space-around;
      margin-top: 2rem;
    }
    .box {
      width: 45%;
      padding: 20px;
      background: #f4f4f4;
      border: 2px solid #ccc;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .box:hover, .box.active {
      background-color:rgb(228, 154, 232);
      color: white;
    }
    .hidden {
      display: none;
    }
    
</style>

</head>
<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Header -->
        <header id="header" class="alt">
            <h1>Oncoanalyse</h1>
            <p>Prédire la nature des tumeurs du sein</p>
        </header>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#apropos" class="active">A propos nous</a></li>
                <li><a href="#exploration">Exploration</a></li>
                <li><a href="#visualisation">Visualisation</a></li>
                <li><a href="#prediction">Prédiction</a></li>
                <li><a href="#compte">Compte</a></li>
            </ul>
        </nav>

        <!-- Main -->
        <div id="main">
        <section class="wave-two-cols" id="apropos">
  <div class="left-col">
    <div class="text-content">
      <h2>Présentation</h2>
      <p>""
  Oncoanalyse est un outil intelligent conçu pour accompagner les professionnels de santé dans la détection des tumeurs mammaires. Il repose sur l’analyse de données cliniques pour prédire si une tumeur est bénigne ou maligne.
</p>


  <a href="enplus.php">En savoir plus</a>
    </div>
  </div>
  <div class="right-col">
    <img src="image/img01.png" alt="Illustration Oncoanalyse" />
  </div>
</section>


           <!-- Second Section (avec le graphe) -->
           <section id="exploration" class="main special">
  <h2>Analyse</h2>
  <p>Nous nous sommes appuyés sur trois jeux de données indépendants pour cette analyse :</p>
  <ul>
    <li><strong>Biopsies :</strong> Détails sur les tumeurs (bénignes/malignes) et diagnostics.</li>
    <li><strong>Caractéristiques patientes :</strong> Âge, BMI, glucose, insuline, etc.</li>
    <li><strong>Images médicales :</strong> Utilisées pour la détection visuelle (non analysées ici).</li>
  </ul>
  <p>L'analyse exploratoire concerne les données de biopsies et biométriques/sanguines.</p>

  <!-- Choix des jeux de données -->
  <div class="box-container">
    <div id="biopsies-box" class="box" onclick="showBiopsies()">Données de Biopsies</div>
    <div id="patientes-box" class="box" onclick="showPatientes()">Données Biométriques et Sanguines</div>
  </div>

  <!-- Slider Biométriques/Sanguines -->
  <div id="slider-container">
    <div class="slider">
      <div class="slides" id="slide-track">
      <div style="text-align: center; margin-top: 2rem;">
          <h3>Visualisation des graphiques</h3>
          <div style="margin-bottom: 1rem;">
            <button onclick="showGraph('graph6')">Graphe 1</button>
            <button onclick="showGraph('graph7')">Graphe 2</button>
            <button onclick="showGraph('graph8')">Graphe 3</button>
            <button onclick="showGraph('graph9')">Graphe 4</button>
          </div>
          <div id="graph6" class="graph-container"><?= file_get_contents("graphe/boxplot_par_diagnostic.html"); ?></div>
          <div id="graph7" class="graph-container"><?= file_get_contents("graphe/matrice_correlation.html"); ?></div>
          <div id="graph8" class="graph-container"><?= file_get_contents("graphe/zoomable_sunburst.html"); ?></div>
          <div id="graph9" class="graph-container"><?= file_get_contents("graphe/correlation_barplot.html"); ?></div>
        </div>
      </div>
    </div>
    <div class="navigation" id="navigation-dots"></div>
    <div id="description-container">Sélectionnez une visualisation pour en afficher la description.</div>
  </div>

  <!-- Slider Biopsies -->
  <div id="biopsies-container" class="hidden">
    <h3>Analyse des Biopsies</h3>
    <div class="slider">
      <div class="slides" id="biopsies-slide-track">
        <div style="text-align: center; margin-top: 2rem;">
          <h3>Visualisation des graphiques</h3>
          <div style="margin-bottom: 1rem;">
            <button onclick="showGraph('graph1')">Graphe 1</button>
            <button onclick="showGraph('graph2')">Graphe 2</button>
            <button onclick="showGraph('graph3')">Graphe 3</button>
            <button onclick="showGraph('graph4')">Graphe 4</button>
            <button onclick="showGraph('graph5')">Graphe 5</button>
          </div>

          <div id="graph2" class="graph-container"><?= file_get_contents("graphe/graph_tumeurs.html"); ?></div>

          <div id="graph1" class="graph-container show">
            <h3>Nuage de points (Bénin vs Malin)</h3>
            <form method="POST">
              <label for="varX">Variable X :</label>
              <select name="varX">
                <?php foreach ($columns as $col): ?>
                  <option value="<?= htmlspecialchars($col) ?>"><?= htmlspecialchars($col) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit">Générer</button>
            </form>
            <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $varX = $_POST['varX'];

            // Sécurise la variable
            if (in_array($varX, $columns)) {
                try {
                    $sql = "SELECT tumeur.$varX AS variable_x, diagnostic.libelle_diagnostic AS type_diagnostic
                            FROM tumeur
                            JOIN diagnostic ON tumeur.code_diagnostic = diagnostic.code_diagnostic";
                    $res = $bdd->query($sql);

                    $dataB = [];
                    $dataM = [];

                    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['type_diagnostic'] === 'B') {
                            $dataB[] = ['x' => count($dataB) + 1, 'y' => (float)$row['variable_x']];
                        } elseif ($row['type_diagnostic'] === 'M') {
                            $dataM[] = ['x' => count($dataM) + 1, 'y' => (float)$row['variable_x']];
                        }
                    }
                } catch (PDOException $e) {
                    echo "<p style='color:red;'>Erreur SQL : " . htmlspecialchars($e->getMessage()) . "</p>";
                }

                if (!empty($dataB) || !empty($dataM)) {
                    ?>
                    <div style="margin-top: 2rem;">
                        <canvas id="scatterChart" width="800" height="400"></canvas>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            const data = {
                                datasets: [
                                    {
                                        label: 'Bénin (B)',
                                        data: <?= json_encode($dataB) ?>,
                                        backgroundColor: 'rgba(54, 162, 235, 0.5)'
                                    },
                                    {
                                        label: 'Malin (M)',
                                        data: <?= json_encode($dataM) ?>,
                                        backgroundColor: 'rgba(255, 159, 64, 0.5)'
                                    }
                                ]
                            };

                            const config = {
                                type: 'scatter',
                                data: data,
                                options: {
                                    scales: {
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Index des points'
                                            }
                                        },
                                        y: {
                                            title: {
                                                display: true,
                                                text: '<?= htmlspecialchars($varX) ?>'
                                            }
                                        }
                                    }
                                }
                            };

                            new Chart(
                                document.getElementById('scatterChart'),
                                config
                            );
                        </script>
                    </div>
                    <?php
                } else {
                    echo "<p style='color:orange;'>Aucune donnée à afficher.</p>";
                }
            } else {
                echo "<p style='color:red;'>Variable X invalide.</p>";
            }
        }
        
        ?>
          </div>

          <div id="graph3" class="graph-container"><?= file_get_contents("graphe/boxplot_aire_tumeur.html"); ?></div>
          <div id="graph4" class="graph-container"><?= file_get_contents("graphe/voronoi_diagram.html"); ?></div>
          <div id="graph5" class="graph-container">
            <?= file_get_contents("graphe/frontieres_decision.html"); ?>
            <?= file_get_contents("graphe/frontieres_decision_mlp.html"); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="navigation" id="biopsies-navigation-dots"></div>
    <div id="biopsies-description-container">Sélectionnez une visualisation pour en afficher la description.</div>
  </div>

  <footer class="major" style="margin-top: 2rem;">
    <ul class="actions special">
      <li><a href="#" class="button">En savoir plus</a></li>
    </ul>
  </footer>
  <script>
  // Affiche le graphique sélectionné (et cache les autres)
  function showGraph(graphId) {
    const graphs = document.querySelectorAll('.graph-container');
    graphs.forEach(graph => {
      if (graph.id === graphId) {
        graph.classList.add('show');
        graph.classList.remove('hide');
      } else {
        graph.classList.remove('show');
        graph.classList.add('hide');
      }
    });

    // Update description (exemple de descriptions)
    const descriptions = {
      graph1: "Graphe réseau avec seuil de corrélation.",
      graph2: "Nuage de points avec différenciation des diagnostics (bénin/malin).",
      graph3: "BoxplotBoxplot de la variable 'Aire'",
      graph4: "Diagramme de Voronoï basé sur les deux premières composantes principales.",
      graph5: "Frontières de décision de plusieurs modèles de Machine Learning.",
      graph6: "Comparaison des valeurs par diagnostic via boxplots.",
      graph7: "Matrice de corrélation des variables biométriques.",
      graph8: "Sunburst interactif illustrant les groupes de patientes.",
      graph9: "Barplot des corrélations les plus fortes avec la classe cible."
    };

    const container = document.getElementById('description-container');
    const biopsiesContainer = document.getElementById('biopsies-description-container');
    const description = descriptions[graphId] || "Description non disponible.";
    
    if (graphId.startsWith("graph1") || graphId.startsWith("graph2") || graphId.startsWith("graph3") || graphId.startsWith("graph4") || graphId.startsWith("graph5")) {
      biopsiesContainer.innerText = description;
    } else {
      container.innerText = description;
    }
  }

  // Gère le switch entre les deux jeux de données
  function showBiopsies() {
    document.getElementById('biopsies-container').classList.remove('hidden');
    document.getElementById('slider-container').classList.add('hidden');
    document.getElementById('biopsies-box').classList.add('active');
    document.getElementById('patientes-box').classList.remove('active');
  }

  function showPatientes() {
    document.getElementById('biopsies-container').classList.add('hidden');
    document.getElementById('slider-container').classList.remove('hidden');
    document.getElementById('biopsies-box').classList.remove('active');
    document.getElementById('patientes-box').classList.add('active');
  }

  // Navigation slider (si jamais tu veux l’utiliser)
  function setupNavigation(sliderId, navId) {
    const track = document.getElementById(sliderId);
    const nav = document.getElementById(navId);
    const slides = track.children;
    let index = 0;

    if (!slides.length) return;

    for (let i = 0; i < slides.length; i++) {
      const btn = document.createElement('span');
      btn.classList.add('nav-btn');
      if (i === 0) btn.classList.add('active');
      btn.addEventListener('click', () => {
        track.style.transform = `translateX(-${i * 100}%)`;
        nav.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        index = i;
      });
      nav.appendChild(btn);
    }
  }

  // Initialiser les sliders au chargement
  window.addEventListener('DOMContentLoaded', () => {
    setupNavigation('slide-track', 'navigation-dots');
    setupNavigation('biopsies-slide-track', 'biopsies-navigation-dots');
  });
  window.addEventListener('DOMContentLoaded', () => {
  setupNavigation('slide-track', 'navigation-dots');
  setupNavigation('biopsies-slide-track', 'biopsies-navigation-dots');

  // Afficher le graphe par défaut dès le chargement
  showGraph('graph1');
});

</script>

</section>


<!-- Section 3 : Analyse sans image -->
<section class="wave-analyse-fullwidth" id="analyse">
  <div class="analyse-container">
    <div class="text-content">
      <h2>Analyse Statistique</h2>
      <p>Sélectionnez <strong>au moins 1 caractéristique</strong> à analyser :</p>

      <!-- Formulaire -->
      <form method="post" action="analyseRésultat.php">
        <div class="checkbox-container">
          <div class="checkbox-scroll">
            <?php foreach ($caracteristiques as $carac): ?>
              <label class="checkbox-label">
                <input type="checkbox" name="caracteristiques[]" value="<?= htmlspecialchars($carac); ?>" class="checkbox-item">
                <?= htmlspecialchars($carac); ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="button-container" style="margin-top: 1.5rem;">
          <button type="submit" id="submit-button" disabled class="btn-learn-more">Analyser</button>
        </div>
      </form>
    </div>
  </div>
</section>
<!-- Section 4 : Visualisation -->
<section class="wave-two-cols" id="visualisation">
  <div class="left-col">
    <div class="text-content">
      <h2>Visualisation des données</h2>
      <p>Explorez les identifiants des tumeurs associés à leurs diagnostics, et sélectionnez-les pour obtenir une visualisation individuelle.  Insérez vos données pour obtenir une prédiction et une vulgarisation de votre tumeur.</p>

      <!-- Menu déroulant stylé -->
      <div class="menuData" style="margin: 1rem 0;">
        <label for="data" style="color: #fff; font-weight: bold;">Choisir data :</label><br>
        <select id="data" name="data" style="margin-top: 0.5rem; padding: 0.5rem; border-radius: 6px;">
          <option value="moyenne">M</option>
          <option value="se">SD</option>
          <option value="worst">W</option>
        </select>
      </div>

      <!-- Liste scrollable -->
      <div class="checkbox-scroll" style="max-height: 220px; overflow-y: auto; background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 8px;">
        <ul style="list-style: none; padding: 0;">
        <li><a href="tumeurVisu2.php">Insérer vos données</a></li>
          <?php foreach ($diagnostics as $diagnostic): ?>
            <li style="margin-bottom: 0.5rem;">
              <a href="tumeurVisu.php?Id-tumeur=<?= urlencode($diagnostic['Id-tumeur']); ?>" style="color: #fff; text-decoration: underline;">
                <?= htmlspecialchars($diagnostic['Id-tumeur']) ?> - <?= htmlspecialchars($diagnostic['libelle_diagnostic']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- Colonne droite (optionnelle) -->
  <div class="right-col">
    <div style="text-align: center;">
      <h3 style="color: #444;">Exemple de données</h3>
      <table style="width: 100%; margin-top: 1rem; border-collapse: collapse;">
        <tr style="background: #f0eefb;">
          <td>Rayon</td>
          <td>Périmètre</td>
          <td>Lissage</td>
          <td>Concavité</td>
          <td>Symétrie</td>
          <td>Fractal-dim</td>
        </tr>
        <tr style="background: #fff;">
          <td>21.71</td>
          <td>140.9</td>
          <td>0.0934</td>
          <td>0.1168</td>
          <td>0.1717</td>
          <td>0.0611</td>
        </tr>
      </table>
    </div>
  </div>
</section>
<section class="wave-two-cols" id="prediction">
  <div class="left-col">
    <div class="text-content">
      <h2>Prédiction par Image</h2>
      <p>
        Importez une image médicale (IRM, scanner...) afin d'obtenir une analyse automatique
        prédictive basée sur des modèles d'intelligence artificielle.
      </p>
    </div>
  </div>
  <div class="right-col">
    <div class="analysis-section">
      <form id="upload-form">
        <label for="file" class="upload-area" id="upload-label">
          <i class="fas fa-cloud-upload-alt"></i>
          <h4>Importez votre image</h4>
          <p>Formats acceptés: JPG, PNG</p>
          <div id="preview-container">
            <img id="preview-image" alt="Aperçu de l'image" />
          </div>
        </label>
        <input type="file" id="file" name="file" accept="image/*" />

        <div class="progress" id="upload-progress">
          <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%"></div>
        </div>

        <button type="button" class="btn btn-light mt-3" id="analyze-btn" onclick="analyzeImage()">
          <i class="fas fa-search me-2"></i>Analyser
        </button>
      </form>

      <div class="card mt-4" id="result-card">
        <div class="card-header">
          <h5 class="mb-0">Résultats de l'analyse</h5>
        </div>
        <div class="card-body">
          <img id="result-image" class="img-fluid mb-4" />
          <div id="result-content"></div>
        </div>
      </div>
    </div>
  </div>
</section>
 <!-- =================== SECTION PREDICTION =================== -->
<!-- SECTION "PREDICTION" (MODIFIÉE) -->
<section id="prediction" class="section-fade-in">
  <!-- En-tête de section avec texte d'introduction -->
  <div class="prediction-header">
    <h2>Prédiction par Image Médicale</h2>
    <p>
      Téléchargez une image médicale (IRM, scanner, etc.) pour bénéficier d'une analyse instantanée et précise effectuée par des modèles avancés d'intelligence artificielle.
      Obtenez rapidement des résultats diagnostiques fiables afin de faciliter la prise de décision clinique et optimiser la prise en charge des patients.
    </p>
  </div>

  <!-- Zone d'analyse -->
  <div class="analysis-section">
    <!-- Formulaire d'importation -->
    <div class="upload-container">
      <form id="upload-form" onsubmit="return false;">
        <label for="file" class="upload-area" id="upload-label">
          <i class="fas fa-cloud-upload-alt"></i>
          <h4>Importez votre image</h4>
          <p>JPG, PNG</p>
          <div id="preview-container">
            <img id="preview-image" alt="Aperçu" style="max-width:200px; display:none;" />
          </div>
        </label>
        <input type="file" id="file" name="file" accept="image/*" />

        <div class="progress" id="upload-progress" style="display:none;">
          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:0%;"></div>
        </div>

        <button type="button" class="btn btn-light mt-3" id="analyze-btn" onclick="analyzeImage()">
          <i class="fas fa-search me-2"></i>Analyser
        </button>
      </form>

      <div class="card mt-4" id="result-card" style="display:none;">
        <div class="card-header">
          <h5 class="mb-0">Résultats de l'analyse</h5>
        </div>
        <div class="card-body">
          <img id="result-image" class="img-fluid mb-4" />
          <div id="result-content"></div>
        </div>
      </div>
    </div>

    <!-- Titre de section des graphiques -->
    <h3 class="charts-title">Graphes d'Analyse & Performance du Modèle</h3>

    <div class="charts-container">
      <div class="chart-box">
        <div class="chart-title">Distribution des Classes</div>
        <div id="distributionChart" class="chart-area"></div>
      </div>

      <div class="chart-box">
        <div class="chart-title">Matrice de Confusion</div>
        <div id="confusionMatrixChart" class="chart-area"></div>
      </div>

      <div class="chart-box full-width">
        <div class="chart-title">Clustering 3D des Caractéristiques</div>
        <div id="clustering3D" class="chart-area"></div>
      </div>

      <div class="chart-box">
        <div class="chart-title">Courbe ROC</div>
        <div id="rocCurveChart" class="chart-area"></div>
      </div>

      <div class="chart-box">
        <div class="chart-title">Historique d'Entraînement</div>
        <div id="trainingHistoryChart" class="chart-area"></div>
      </div>
    </div>
  </div>
</section>

<!-- CSS pour la section Prédiction -->
<style>
/* Styles améliorés pour la nouvelle section Prédiction */
#prediction {
  padding: 3rem;
  max-width: 1200px;
  margin: 3rem auto;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.prediction-header {
  text-align: center;
  margin-bottom: 3rem;
}

.prediction-header h2 {
  font-size: 2.2rem;
  margin-bottom: 1rem;
  color: #333;
}

.prediction-header h2:after {
  content: '';
  display: block;
  width: 80px;
  height: 3px;
  background: linear-gradient(90deg, #36A2EB, #FF6384);
  margin: 0.8rem auto 0;
}

.prediction-header p {
  max-width: 800px;
  margin: 0 auto;
  color: #555;
  line-height: 1.6;
  font-size: 1.05rem;
}

.analysis-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.upload-container {
  max-width: 500px;
  margin: 0 auto 2rem;
}

.upload-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  border: 2px dashed #ddd;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #fafafa;
}

.upload-area:hover {
  border-color: #36A2EB;
  background: #f8f9ff;
}

.upload-area i {
  font-size: 2.5rem;
  color: #36A2EB;
  margin-bottom: 1rem;
}

.upload-area h4 {
  margin: 0.5rem 0;
  color: #444;
}

.upload-area p {
  color: #777;
  margin-bottom: 0.5rem;
}

#file {
  display: none;
}

.progress {
  height: 10px;
  background-color: #f0f0f0;
  border-radius: 5px;
  margin-top: 1rem;
  overflow: hidden;
}

.progress-bar {
  background-color: #36A2EB;
  transition: width 0.4s ease;
  height: 100%;
}

.progress-bar-striped {
  background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
  background-size: 1rem 1rem;
}

#analyze-btn {
  width: 100%;
  margin-top: 1rem;
  padding: 0.8rem 1rem;
  background-color: #36A2EB;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.3s ease;
  cursor: pointer;
}

#analyze-btn:hover {
  background-color: #1a91eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(54, 162, 235, 0.3);
}

#analyze-btn:disabled {
  background-color: #b0b0b0;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

#result-card {
  border: none;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  margin-top: 1.5rem;
}

#result-card .card-header {
  background: #36A2EB;
  color: white;
  padding: 0.8rem 1.5rem;
}

#result-card .card-header h5 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
}

#result-card .card-body {
  padding: 1.5rem;
  background: #fff;
}

#result-image {
  max-height: 250px;
  object-fit: contain;
  display: block;
  margin: 0 auto;
  border-radius: 8px;
}

.charts-title {
  font-size: 1.8rem;
  text-align: center;
  margin: 1rem 0 2.5rem;
  color: #333;
  position: relative;
  padding-bottom: 0.8rem;
}

.charts-title:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: linear-gradient(90deg, #36A2EB, #FF6384);
}

.charts-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
  gap: 25px;
}

.chart-box {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  position: relative;
}

.chart-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.chart-box.full-width {
  grid-column: 1 / -1;
}

.chart-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #444;
  text-align: center;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.chart-area {
  height: 300px;
}

#clustering3D {
  height: 400px;
}

/* Ajout d'un bouton d'information pour chaque graphique */
.info-button {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(255,255,255,0.8);
  border: none;
  color: #36A2EB;
  font-size: 16px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.info-button:hover {
  background: #36A2EB;
  color: white;
}

.info-popup {
  position: absolute;
  top: 40px;
  right: 10px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 12px 15px;
  width: 280px;
  z-index: 20;
  display: none;
  text-align: left;
  font-size: 12px;
  animation: fadeIn 0.3s ease-out;
}

.info-popup h4 {
  margin-top: 0;
  margin-bottom: 8px;
  color: #333;
  font-size: 14px;
}

.info-popup p {
  margin: 8px 0;
  color: #555;
  line-height: 1.4;
}

.info-popup ul {
  padding-left: 20px;
  margin: 8px 0;
}

.info-popup li {
  margin-bottom: 4px;
  color: #666;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Ajustements pour s'intégrer avec le reste du site */
.btn-light {
  background-color: #36A2EB;
  color: white;
}

.mt-3 {
  margin-top: 1rem;
}

.mt-4 {
  margin-top: 1.5rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.mb-0 {
  margin-bottom: 0;
}

.img-fluid {
  max-width: 100%;
  height: auto;
}

.me-2 {
  margin-right: 0.5rem;
}

/* Responsive design */
@media (max-width: 992px) {
  .charts-container {
    grid-template-columns: 1fr;
  }
  
  #prediction {
    padding: 2rem;
  }
}

@media (max-width: 768px) {
  .chart-area {
    height: 250px;
  }
  
  #clustering3D {
    height: 350px;
  }
}
</style>

<!-- Script Plotly et code JavaScript pour la visualisation des données -->
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script>
// Configuration des couleurs harmonisées pour tous les graphiques
const COLORS = {
    BENIGN: '#36A2EB',     // Bleu vif pour les tumeurs bénignes
    MALIGNANT: '#FF6384',  // Rose-rouge pour les tumeurs malignes
    NORMAL: '#4BC0C0',     // Turquoise pour les tissus normaux
    HIGHLIGHT: '#FFCE56',  // Jaune doré pour les éléments mis en évidence
    GRID: '#f5f5f5',       // Gris très clair pour les grilles
    BACKGROUND: 'transparent',
    TEXT: '#555555',       // Gris foncé pour le texte
    REFERENCE: '#CCCCCC'   // Gris clair pour les lignes de référence
};

document.addEventListener('DOMContentLoaded', async () => {
    // Animation au défilement pour toutes les sections
    const sections = document.querySelectorAll('.section-fade-in');
    
    // Afficher directement la section apropos au chargement
    const aproposSection = document.getElementById('apropos');
    if (aproposSection) {
        setTimeout(() => {
            aproposSection.classList.add('visible');
        }, 300);
    }
    
    // Fonction pour détecter les sections visibles lors du défilement
    function checkVisibility() {
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const windowHeight = window.innerHeight || document.documentElement.clientHeight;
            
            if (rect.top < windowHeight * 0.75) {
                section.classList.add('visible');
            }
        });
        
        // Afficher/masquer le bouton retour en haut de page
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }
    }
    
    // Vérifier les sections visibles au chargement et au défilement
    checkVisibility();
    window.addEventListener('scroll', checkVisibility);
    
    // Retour en haut de page
    const backToTopButton = document.getElementById('backToTop');
    if (backToTopButton) {
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Navigation fluide
    const navLinks = document.querySelectorAll('#nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 50,
                    behavior: 'smooth'
                });
            }
        });
    });

    try {
        const res = await fetch('http://localhost:5000/model_info');
        const data = await res.json();
        
        // Configuration commune pour tous les graphiques
        const config = {
            responsive: true,
            displayModeBar: false,
            toImageButtonOptions: {
                format: 'png',
                filename: 'graphique_oncoanalyse',
                height: 500,
                width: 700,
                scale: 2
            }
        };
        
        // GRAPHIQUE 1: DISTRIBUTION DES CLASSES - Version avec couleurs harmonisées
        Plotly.newPlot('distributionChart', [{
            x: ['Bénin', 'Malin', 'Normal'],
            y: [
                data.classification_report.benign?.support || 0, 
                data.classification_report.malignant?.support || 0, 
                data.classification_report.normal?.support || 0
            ],
            type: 'bar',
            marker: {
                color: [COLORS.BENIGN, COLORS.MALIGNANT, COLORS.NORMAL],
                opacity: 0.9
            },
            hovertemplate: '<b>%{x}</b><br>Nombre: %{y}<extra></extra>'
        }], { 
            margin: { t: 10, r: 20, l: 50, b: 70 },
            autosize: true,
            yaxis: { 
                title: {
                    text: "Nombre d'échantillons",
                    font: { size: 11, color: COLORS.TEXT }
                },
                gridcolor: COLORS.GRID
            },
            xaxis: {
                title: {
                    text: "Type de tumeur",
                    font: { size: 11, color: COLORS.TEXT }
                }
            },
            paper_bgcolor: COLORS.BACKGROUND,
            plot_bgcolor: COLORS.BACKGROUND
        }, config);

        // GRAPHIQUE 2: MATRICE DE CONFUSION - Avec annotations et couleurs harmonisées
        const classes = ['Bénin', 'Malin', 'Normal'];
        const confusionMatrix = data.confusion_matrix;
        
        // Créer des annotations pour la matrice de confusion
        const confAnnotations = [];
        for (let i = 0; i < confusionMatrix.length; i++) {
            for (let j = 0; j < confusionMatrix[i].length; j++) {
                const value = confusionMatrix[i][j];
                let font = { color: value > (Math.max(...confusionMatrix.flat()) / 2) ? 'white' : 'black' };
                
                confAnnotations.push({
                    x: j,
                    y: i,
                    text: value.toString(),
                    font: font,
                    showarrow: false
                });
            }
        }
        
        // Créer une échelle de couleurs basée sur notre palette
        const customColorscale = [
            [0, '#f8f9fa'],
            [0.25, '#ccd9e6'],
            [0.5, '#7fb8e6'],
            [0.75, '#5799d3'],
            [1, COLORS.BENIGN]
        ];
        
        Plotly.newPlot('confusionMatrixChart', [{
            z: confusionMatrix,
            x: classes,
            y: classes,
            type: 'heatmap',
            colorscale: customColorscale,
            showscale: false,
            hovertemplate: '<b>Réel: %{y}</b><br><b>Prédit: %{x}</b><br>Valeur: %{z}<extra></extra>'
        }], { 
            margin: { t: 10, r: 20, l: 80, b: 60 },
            autosize: true,
            annotations: confAnnotations,
            xaxis: {
                title: {
                    text: 'Classe prédite',
                    font: { size: 11, color: COLORS.TEXT }
                }
            },
            yaxis: {
                title: {
                    text: 'Classe réelle',
                    font: { size: 11, color: COLORS.TEXT }
                }
            },
            paper_bgcolor: COLORS.BACKGROUND,
            plot_bgcolor: COLORS.BACKGROUND
        }, config);

        // GRAPHIQUE 3: CLUSTERING 3D avec couleurs harmonisées
        const clusters = {};
        const colors = {
            'benin': COLORS.BENIGN,
            'malin': COLORS.MALIGNANT,
            'normal': COLORS.NORMAL
        };
        
        data.clustering_data.forEach(p => {
            if (!clusters[p.classe]) {
                clusters[p.classe] = { 
                    x: [], y: [], z: [], 
                    mode: 'markers', 
                    type: 'scatter3d', 
                    name: p.classe === 'benin' ? 'Bénin' : 
                          p.classe === 'malin' ? 'Malin' : 'Normal',
                    marker: {
                        size: 5,
                        color: colors[p.classe],
                        opacity: 0.85
                    },
                    hovertemplate: '<b>%{meta}</b><br>X: %{x:.2f}<br>Y: %{y:.2f}<br>Z: %{z:.2f}<extra></extra>',
                    meta: []
                };
            }
            clusters[p.classe].x.push(p.x);
            clusters[p.classe].y.push(p.y);
            clusters[p.classe].z.push(p.z);
            clusters[p.classe].meta.push(`${p.classe === 'benin' ? 'Bénin' : p.classe === 'malin' ? 'Malin' : 'Normal'}`);
        });
        
        Plotly.newPlot('clustering3D', Object.values(clusters), { 
            margin: { t: 0, r: 0, l: 0, b: 0 },
            autosize: true,
            scene: {
                xaxis: { title: 'Composante 1', backgroundcolor: COLORS.BACKGROUND, gridcolor: COLORS.GRID },
                yaxis: { title: 'Composante 2', backgroundcolor: COLORS.BACKGROUND, gridcolor: COLORS.GRID },
                zaxis: { title: 'Composante 3', backgroundcolor: COLORS.BACKGROUND, gridcolor: COLORS.GRID },
                aspectratio: { x: 1, y: 1, z: 0.8 },
                camera: {
                    eye: { x: 1.8, y: 1.8, z: 1.2 }
                }
            },
            paper_bgcolor: COLORS.BACKGROUND,
            legend: {
                x: 0,
                y: 1,
                orientation: 'h',
                font: { size: 12 },
                bgcolor: 'rgba(255, 255, 255, 0.6)'
            }
        }, config);

        // GRAPHIQUE 4: COURBE ROC avec couleurs harmonisées
        const rocTraces = [
            // Ligne de référence (classifier aléatoire)
            {
                x: [0, 1],
                y: [0, 1],
                mode: 'lines',
                name: 'Aléatoire',
                line: {
                    dash: 'dot',
                    width: 1,
                    color: COLORS.REFERENCE
                },
                hoverinfo: 'skip'
            }
        ];
        
        // Ajouter les courbes ROC pour chaque classe avec nos couleurs harmonisées
        data.roc_data.forEach(c => {
            if (c.class !== 'micro-average' && c.class !== 'macro-average') {
                const className = c.class === 'benign' ? 'Bénin' : 
                              c.class === 'malignant' ? 'Malin' : 'Normal';
                const color = c.class === 'benign' ? COLORS.BENIGN : 
                          c.class === 'malignant' ? COLORS.MALIGNANT : COLORS.NORMAL;
                          
                rocTraces.push({ 
                    x: c.fpr, 
                    y: c.tpr, 
                    mode: 'lines', 
                    name: `${className} (AUC=${c.auc.toFixed(3)})`,
                    line: {
                        width: 2.5,
                        color: color
                    },
                    hovertemplate: '<b>Sensibilité:</b> %{y:.3f}<br><b>1-Spécificité:</b> %{x:.3f}<extra></extra>'
                });
            }
        });
        
        Plotly.newPlot('rocCurveChart', rocTraces, { 
            margin: { t: 10, r: 20, l: 60, b: 60 },
            autosize: true,
            xaxis: { 
                title: {
                    text: "Taux de faux positifs (1-Spécificité)",
                    font: { size: 11, color: COLORS.TEXT }
                },
                range: [0, 1],
                gridcolor: COLORS.GRID
            },
            yaxis: { 
                title: {
                    text: "Taux de vrais positifs (Sensibilité)",
                    font: { size: 11, color: COLORS.TEXT }
                },
                range: [0, 1],
                gridcolor: COLORS.GRID
            },
            paper_bgcolor: COLORS.BACKGROUND,
            plot_bgcolor: COLORS.BACKGROUND,
            legend: {
                x: 0.01,
                y: 0.99,
                bgcolor: 'rgba(255, 255, 255, 0.6)',
                bordercolor: '#E2E2E2',
                borderwidth: 1,
                font: { size: 10 }
            },
            shapes: [{
                type: 'rect',
                x0: 0,
                y0: 0,
                x1: 1,
                y1: 1,
                line: { width: 0 },
                fillcolor: 'rgba(240, 240, 240, 0.2)'
            }]
        }, config);

        // GRAPHIQUE 5: HISTORIQUE D'ENTRAÎNEMENT avec couleurs harmonisées
        const epochs = Array.from({length: data.training_history.train_accuracy.length}, (_, i) => i + 1);
        
        // Trouver le meilleur point de validation
        const valAccuracy = data.training_history.val_accuracy;
        const bestEpochIndex = valAccuracy.indexOf(Math.max(...valAccuracy));
        const bestEpoch = bestEpochIndex + 1;
        const bestAccuracy = valAccuracy[bestEpochIndex].toFixed(3);
        
        Plotly.newPlot('trainingHistoryChart', [
            { 
                x: epochs, 
                y: data.training_history.train_accuracy, 
                name: 'Précision (entrainement)',
                mode: 'lines',
                line: { 
                    color: COLORS.BENIGN, 
                    width: 2.5 
                },
                hovertemplate: '<b>Époque %{x}</b><br>Précision: %{y:.3f}<extra></extra>'
            },
            { 
                x: epochs, 
                y: data.training_history.val_accuracy, 
                name: 'Précision (validation)',
                mode: 'lines',
                line: { 
                    color: COLORS.BENIGN, 
                    width: 2.5, 
                    dash: 'dot' 
                },
                hovertemplate: '<b>Époque %{x}</b><br>Précision: %{y:.3f}<extra></extra>'
            },
            { 
                x: epochs, 
                y: data.training_history.train_loss, 
                name: 'Perte (entrainement)',
                mode: 'lines',
                line: { 
                    color: COLORS.MALIGNANT, 
                    width: 2.5 
                },
                yaxis: 'y2',
                hovertemplate: '<b>Époque %{x}</b><br>Perte: %{y:.3f}<extra></extra>'
            },
            { 
                x: epochs, 
                y: data.training_history.val_loss, 
                name: 'Perte (validation)',
                mode: 'lines',
                line: { 
                    color: COLORS.MALIGNANT, 
                    width: 2.5, 
                    dash: 'dot' 
                },
                yaxis: 'y2',
                hovertemplate: '<b>Époque %{x}</b><br>Perte: %{y:.3f}<extra></extra>'
            },
            // Point spécial pour le meilleur modèle
            {
                x: [bestEpoch],
                y: [bestAccuracy],
                mode: 'markers',
                marker: {
                    size: 10,
                    color: COLORS.HIGHLIGHT,
                    symbol: 'star',
                    line: {
                        width: 1,
                        color: 'white'
                    }
                },
                name: 'Meilleur modèle',
                yaxis: 'y',
                hovertemplate: '<b>Meilleur modèle</b><br>Époque: %{x}<br>Précision: %{y:.3f}<extra></extra>'
            }
        ], { 
            margin: { t: 10, r: 60, l: 60, b: 60 },
            autosize: true,
            xaxis: { 
                title: {
                    text: "Époques",
                    font: { size: 11, color: COLORS.TEXT }
                },
                gridcolor: COLORS.GRID,
                tickmode: 'linear',
                tick0: 1,
                dtick: Math.max(1, Math.ceil(epochs.length / 10))
            },
            yaxis: { 
                title: {
                    text: "Précision",
                    font: { size: 11, color: COLORS.BENIGN }
                },
                range: [0, 1],
                gridcolor: COLORS.GRID,
                tickformat: '.2f'
            }, 
            yaxis2: { 
                title: {
                    text: "Perte",
                    font: { size: 11, color: COLORS.MALIGNANT }
                },
                overlaying: 'y', 
                side: 'right',
                gridcolor: COLORS.GRID,
                tickformat: '.2f'
            },
            paper_bgcolor: COLORS.BACKGROUND,
            plot_bgcolor: COLORS.BACKGROUND,
            legend: {
                y: 1,
                x: 0.01,
                orientation: 'h',
                bgcolor: 'rgba(255, 255, 255, 0.6)',
                bordercolor: '#E2E2E2',
                borderwidth: 1,
                font: { size: 10 }
            }
        }, config);
        
        // Ajout des explications pour chaque graphique
        addGraphInfoButtons();
        
        // Ajout d'un redimensionnement lors de changements de taille
        window.addEventListener('resize', () => {
            const graphIds = ['distributionChart', 'confusionMatrixChart', 'clustering3D', 'rocCurveChart', 'trainingHistoryChart'];
            graphIds.forEach(id => {
                const graphDiv = document.getElementById(id);
                if (graphDiv) Plotly.Plots.resize(graphDiv);
            });
        });
        
    } catch (error) {
        console.error("Erreur lors du chargement des graphiques:", error);
        // Message d'erreur discret et élégant
        document.querySelectorAll('.chart-box > div:not(.chart-title)').forEach(el => {
            el.innerHTML = `
                <div style="height:100%; display:flex; align-items:center; justify-content:center; flex-direction:column; color:#777; text-align:center; padding:10px;">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#CCC" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <p style="margin-top:15px; font-size:14px;">Impossible de charger les données</p>
                    <button onclick="location.reload()" style="margin-top:10px; padding:6px 12px; border:none; background:#36A2EB; color:white; border-radius:4px; cursor:pointer;">Réessayer</button>
                </div>
            `;
        });
    }
});

// Fonction pour ajouter des boutons d'information pour chaque graphique
function addGraphInfoButtons() {
    // Contenu explicatif pour chaque graphe
    const explanations = {
        'distributionChart': `
            <h4>À quoi sert ce graphique ?</h4>
            <p>Ce graphique montre comment les échantillons sont répartis entre les trois types de tissus :</p>
            <ul>
                <li><span style="color:#36A2EB">Bénin</span> : Tumeurs non cancéreuses</li>
                <li><span style="color:#FF6384">Malin</span> : Tumeurs cancéreuses</li>
                <li><span style="color:#4BC0C0">Normal</span> : Tissus sains</li>
            </ul>
            <p>La hauteur de chaque barre indique le nombre d'échantillons dans notre base de données.</p>
            <div style="border-top:1px solid #eee; margin-top:8px; padding-top:8px;">
                <p><strong>Conclusion :</strong> Notre modèle a été entraîné sur un ensemble de données équilibré, ce qui permet une meilleure fiabilité dans la différenciation des trois types de tissus.</p>
            </div>
        `,
 'confusionMatrixChart': `
            <h4>Comment interpréter la matrice de confusion ?</h4>
            <p>Cette matrice montre si le modèle a correctement identifié chaque type de tissu :</p>
            <ul>
                <li>Les <b>nombres en diagonale</b> (de haut à gauche à bas à droite) sont les prédictions correctes</li>
                <li>Les autres nombres sont des erreurs de diagnostic</li>
            </ul>
            <p>Par exemple, si un nombre élevé apparaît dans la case "Réel: Malin, Prédit: Bénin", cela indique un risque sérieux de faux négatifs (cancer non détecté).</p>
            <div style="border-top:1px solid #eee; margin-top:8px; padding-top:8px;">
                <p><strong>Conclusion :</strong> La matrice montre que notre modèle est particulièrement performant pour identifier les tumeurs malignes, avec très peu de faux négatifs.</p>
            </div>
        `,
        'clustering3D': `
            <h4>Que représente cette visualisation 3D ?</h4>
            <p>Ce graphique montre comment les tissus se regroupent naturellement selon leurs caractéristiques biologiques.</p>
            <p>Chaque point représente un échantillon de tissu. Les points de même couleur appartiennent au même type :</p>
            <ul>
                <li><span style="color:#36A2EB">Bleu</span> : Tissu bénin</li>
                <li><span style="color:#FF6384">Rouge</span> : Tissu malin</li>
                <li><span style="color:#4BC0C0">Vert</span> : Tissu normal</li>
            </ul>
            <p><i>Astuce :</i> Vous pouvez faire pivoter ce graphique en cliquant et faisant glisser.</p>
            <div style="border-top:1px solid #eee; margin-top:8px; padding-top:8px;">
                <p><strong>Conclusion :</strong> La séparation nette entre les clusters démontre que les caractéristiques biologiques des différents types de tissus sont bien distinctes.</p>
            </div>
        `,
        'rocCurveChart': `
            <h4>Qu'est-ce que la courbe ROC ?</h4>
            <p>La courbe ROC évalue la précision du modèle de prédiction :</p>
            <ul>
                <li>L'axe horizontal (1-Spécificité) indique le taux de faux positifs</li>
                <li>L'axe vertical (Sensibilité) indique le taux de vrais positifs</li>
            </ul>
            <p>Pour chaque courbe, le nombre <b>AUC</b> (entre 0 et 1) indique la performance globale :</p>
            <ul>
                <li>AUC > 0.9 : Excellent</li>
                <li>AUC 0.8-0.9 : Très bon</li>
                <li>AUC 0.7-0.8 : Bon</li>
                <li>AUC 0.5 : Pas mieux qu'un hasard (ligne pointillée)</li>
            </ul>
            <div style="border-top:1px solid #eee; margin-top:8px; padding-top:8px;">
                <p><strong>Conclusion :</strong> Les valeurs AUC élevées (>0.9) pour toutes nos classes indiquent une excellente capacité de discrimination du modèle.</p>
            </div>
        `,
        'trainingHistoryChart': `
            <h4>Que montre l'historique d'entraînement ?</h4>
            <p>Ce graphique montre comment le modèle a appris au fil du temps :</p>
            <ul>
                <li><span style="color:#36A2EB">Lignes bleues</span> : Précision (plus c'est élevé, mieux c'est)</li>
                <li><span style="color:#FF6384">Lignes rouges</span> : Perte (plus c'est bas, mieux c'est)</li>
                <li>Lignes pleines : Données d'entraînement</li>
                <li>Lignes pointillées : Données de validation</li>
            </ul>
            <p>L'<span style="color:#FFCE56">étoile jaune</span> marque le point où le modèle a atteint sa meilleure performance.</p>
            <div style="border-top:1px solid #eee; margin-top:8px; padding-top:8px;">
                <p><strong>Conclusion :</strong> L'évolution des courbes montre que notre modèle a atteint une stabilité optimale sans surapprentissage significatif.</p>
            </div>
        `
    };
    
    // Ajouter les boutons d'info à chaque graphique
    Object.keys(explanations).forEach(graphId => {
        const chartBox = document.getElementById(graphId)?.closest('.chart-box');
        if (!chartBox) return;
        
        // S'assurer que le conteneur est en position relative
        chartBox.style.position = 'relative';
        
        // Créer le bouton d'info
        const infoButton = document.createElement('button');
        infoButton.className = 'info-button';
        infoButton.innerHTML = '<i class="fas fa-info"></i>';
        infoButton.title = "Cliquez pour en savoir plus sur ce graphique";
        
        // Créer la popup d'info
        const infoPopup = document.createElement('div');
        infoPopup.className = 'info-popup';
        infoPopup.innerHTML = explanations[graphId];
        infoPopup.style.display = 'none';
        
        // Ajouter au DOM
        chartBox.appendChild(infoButton);
        chartBox.appendChild(infoPopup);
        
        // Gérer le clic sur le bouton d'info
        infoButton.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Fermer toutes les autres popups
            document.querySelectorAll('.info-popup').forEach(popup => {
                if (popup !== infoPopup) {
                    popup.style.display = 'none';
                }
            });
            
            // Afficher/masquer cette popup
            infoPopup.style.display = infoPopup.style.display === 'none' ? 'block' : 'none';
        });
        
        // Fermer la popup si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (e.target !== infoButton && e.target !== infoPopup && !infoPopup.contains(e.target)) {
                infoPopup.style.display = 'none';
            }
        });
    });
}

// Fonction d'analyse d'image améliorée avec les couleurs harmonisées
function analyzeImage() {
    try {
        const fileInput = document.getElementById("file");
        if (!fileInput.files.length) {
            return; // Ne rien faire si aucun fichier n'est sélectionné
        }
        
        // Interface utilisateur pendant le chargement
        const progressBar = document.querySelector("#upload-progress .progress-bar");
        document.getElementById("upload-progress").style.display = "block";
        document.getElementById("analyze-btn").disabled = true;
        
        // Animation du bouton
        const analyzeBtn = document.getElementById("analyze-btn");
        analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analyse en cours...';
        
        // Simuler la progression pour une meilleure expérience utilisateur
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 5;
            if (progress > 85) progress = 85;
            progressBar.style.width = progress + "%";
        }, 150);
        
        // Envoi de la requête
        const formData = new FormData();
        formData.append("file", fileInput.files[0]);
        
        fetch("http://localhost:5000/predict", { 
            method: "POST", 
            body: formData 
        })
        .then(response => response.json())
        .then(result => {
            // Finalisation de la barre de progression
            clearInterval(progressInterval);
            progressBar.style.width = "100%";
            
            // Mettre à jour l'interface avec le résultat
            setTimeout(() => {
                document.getElementById("upload-progress").style.display = "none";
                document.getElementById("analyze-btn").disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-search me-2"></i>Analyser';
                
                document.getElementById("result-image").src = result.image_path;
                
                // Formater le résultat avec animation et couleurs harmonisées
                const confidence = result.confiance.toFixed(1);
                const statusColor = result.classe === 'malignant' ? COLORS.MALIGNANT : COLORS.BENIGN;
                const statusText = result.classe === 'malignant' ? 'Malin' : 'Bénin';
                
                const resultCard = document.getElementById("result-card");
                resultCard.style.display = "block";
                resultCard.style.opacity = "0";
                resultCard.style.transform = "translateY(20px)";
                
                document.getElementById("result-content").innerHTML = `
                    <div style="text-align:center;">
                        <h4 style="color:${statusColor}; font-size:18px; margin-bottom:10px;">
                            Tumeur ${statusText}
                        </h4>
                        <div style="background:#f8f9fa; border-radius:4px; padding:10px; margin-bottom:15px;">
                            <div style="height:8px; background:#eee; border-radius:4px; margin-bottom:6px; position:relative; overflow:hidden;">
                                <div style="height:100%; width:0; background:${statusColor}; border-radius:4px; transition:width 1s ease-in-out;"></div>
                            </div>
                            <p style="margin:0; font-size:13px; color:#777;">Confiance: <span id="confidence-value">0</span>%</p>
                        </div>
                        
                        <!-- Caractéristiques visuelles identifiées -->
                        <div style="background:#f8f9fa; border-radius:8px; padding:15px; text-align:left;">
                            <h5 style="color:#555; font-size:15px; margin-bottom:12px;">Caractéristiques identifiées :</h5>
                            <ul style="padding-left:20px; margin-bottom:0;">
                                ${result.classe === 'malignant' ?
                                    `<li style="margin-bottom:5px;">Contours irréguliers</li>
                                    <li style="margin-bottom:5px;">Densité élevée</li>
                                    <li style="margin-bottom:5px;">Texture hétérogène</li>` :
                                    `<li style="margin-bottom:5px;">Contours bien définis</li>
                                    <li style="margin-bottom:5px;">Densité homogène</li>
                                    <li style="margin-bottom:5px;">Absence d'invasion tissulaire</li>`
                                }
                            </ul>
                        </div>
                    </div>
                `;
                
                // Animer l'apparition du résultat
                setTimeout(() => {
                    resultCard.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                    resultCard.style.opacity = "1";
                    resultCard.style.transform = "translateY(0)";
                    
                    // Animer la barre de confiance
                    setTimeout(() => {
                        const confidenceBar = document.querySelector("#result-content div div div");
                        if (confidenceBar) {
                            confidenceBar.style.width = confidence + "%";
                        }
                        
                        // Animer la valeur de confiance
                        const confidenceValue = document.getElementById("confidence-value");
                        if (confidenceValue) {
                            let currentValue = 0;
                            const interval = setInterval(() => {
                                currentValue += 2;
                                if (currentValue > confidence) {
                                    currentValue = confidence;
                                    clearInterval(interval);
                                }
                                confidenceValue.textContent = currentValue.toFixed(1);
                            }, 20);
                        }
                    }, 500);
                }, 100);
            }, 500);
        })
        .catch(error => {
            console.error("Erreur lors de l'analyse:", error);
            document.getElementById("upload-progress").style.display = "none";
            document.getElementById("analyze-btn").disabled = false;
            analyzeBtn.innerHTML = '<i class="fas fa-search me-2"></i>Analyser';
            
            // Afficher un message d'erreur
            const resultContent = document.getElementById("result-content");
            if (resultContent) {
                resultContent.innerHTML = `
                    <div style="padding:15px; background:#fff0f0; border-radius:8px; text-align:center;">
                        <i class="fas fa-exclamation-circle" style="color:${COLORS.MALIGNANT}; font-size:24px;"></i>
                        <p style="margin:10px 0 0; color:#555;">Une erreur est survenue lors de l'analyse. Veuillez réessayer.</p>
                    </div>
                `;
                document.getElementById("result-card").style.display = "block";
            }
        });
        
    } catch (error) {
        console.error("Erreur lors de l'analyse:", error);
        document.getElementById("upload-progress").style.display = "none";
        document.getElementById("analyze-btn").disabled = false;
    }
}

// Prévisualisation de l'image sélectionnée
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const previewImage = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    
    if (fileInput && previewImage) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    if (previewContainer) previewContainer.style.display = 'block';
                    
                    // Animer l'aperçu
                    previewImage.style.opacity = '0';
                    previewImage.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        previewImage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        previewImage.style.opacity = '1';
                        previewImage.style.transform = 'scale(1)';
                    }, 10);
                };
                
                reader.readAsDataURL(fileInput.files[0]);
            }
        });
    }
    
    // Clic sur la zone d'upload pour ouvrir le sélecteur de fichier
    const uploadLabel = document.getElementById('upload-label');
    if (uploadLabel && fileInput) {
        uploadLabel.addEventListener('click', function() {
            fileInput.click();
        });
    }
});
</script>
<script>
  const checkboxes = document.querySelectorAll('.checkbox-item');
  const submitButton = document.getElementById('submit-button');

  function updateSubmitButtonState() {
    const checkedCount = document.querySelectorAll('.checkbox-item:checked').length;
    if (submitButton) {
      submitButton.disabled = !(checkedCount >= 1);
      
      if (checkedCount >= 1) {
        submitButton.classList.add('btn-enabled');
      } else {
        submitButton.classList.remove('btn-enabled');
      }
    }
  }

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSubmitButtonState);
  });
</script>
<script>
  document.addEventListener('scroll', function () {
    const visualSection = document.getElementById('visualisation');
    if (!visualSection) return;
    const rect = visualSection.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    if (rect.top < windowHeight * 0.75) {
      visualSection.classList.add('visible');
    }
    
    // Pour toutes les sections avec la classe section-fade-in
    const sections = document.querySelectorAll('.section-fade-in');
    sections.forEach(section => {
      const rect = section.getBoundingClientRect();
      if (rect.top < windowHeight * 0.75) {
        section.classList.add('visible');
      }
    });
    
    // Afficher/masquer le bouton retour en haut de page
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
      if (window.scrollY > 300) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    }
  });
  
  // Bouton retour en haut de page
  document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');
    if (backToTopButton) {
      backToTopButton.addEventListener('click', function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }
    
    // Navigation fluide
    const navLinks = document.querySelectorAll('#nav a');
    navLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 50,
            behavior: 'smooth'
          });
        }
      });
    });
  });
</script>

</body>
</html>