<?php

$repertoire = "./";
$repertoire_fichiers_fonctions = $repertoire . "src/";

$fichiers_fonction = array_filter(scandir($repertoire_fichiers_fonctions), function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
});

$fichiers_page = array_filter(scandir($repertoire), function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
});

foreach ($fichiers_fonction as $fichier_fonction) {
    $contenu_fichier = file_get_contents($repertoire_fichiers_fonctions . $fichier_fonction);
    $tokens = token_get_all($contenu_fichier);
    $fonction_trouve = false;

    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (token_name($token[0]) === "T_FUNCTION") {
                $fonction_trouve = true;
            } elseif ($fonction_trouve && token_name($token[0]) === "T_STRING") {
                $liste_fonctions[$fichier_fonction][$token[1]] = [];

                foreach ($fichiers_page as $page) {
                    $contenu_fichier_page = file_get_contents($repertoire . $page);
                    $page_tokens = token_get_all($contenu_fichier_page);
                    foreach ($page_tokens as $token_page) {
                        if (is_array($token_page) && $token_page[1] == $token[1] && !in_array($page, $liste_fonctions[$fichier_fonction][$token[1]])) {
                            $liste_fonctions[$fichier_fonction][$token[1]][] = $page;
                        }
                    }
                }

                $fonction_trouve = false;
            }
        }
    }
}
echo "<pre>";
print_r($liste_fonctions);
echo "</pre>";
