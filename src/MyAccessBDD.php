<?php
include_once("AccessBDD.php");

/**
 * Classe de construction des requêtes SQL
 * hérite de AccessBDD qui contient les requêtes de base
 * Pour ajouter une requête :
 * - créer la fonction qui crée une requête (prendre modèle sur les fonctions
 *   existantes qui ne commencent pas par 'traitement')
 * - ajouter un 'case' dans un des switch des fonctions redéfinies
 * - appeler la nouvelle fonction dans ce 'case'
 */
class MyAccessBDD extends AccessBDD {

    /**
     * constructeur qui appelle celui de la classe mère
     */
    public function __construct() {
        try {
            parent::__construct();
        }catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * demande de recherche
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return array|null tuples du résultat de la requête ou null si erreur
     * @override
     */
    protected function traitementSelect(string $table, ?array $champs) : ?array {
        switch ($table) {
            case "livre" :
                return $this->selectAllLivres();
            case "dvd" :
                return $this->selectAllDvd();
            case "revue" :
                return $this->selectAllRevues();
            case "exemplaire" :
                return $this->selectExemplairesRevue($champs);
            case "genre" :
            case "public" :
            case "rayon" :
            case "etat" :
            case "suivi" :
                // select portant sur une table contenant juste id et libelle
                return $this->selectTableSimple($table);
            case "commandedocument" :
                return $this->selectAllCommandesDocument($table);
            case "abonnement" :
                return $this->selectAllAbonnement($table);
            default:
                // cas général
                return $this->selectTuplesOneTable($table, $champs);
        }
    }

    /**
     * demande d'ajout (insert)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples ajoutés ou null si erreur
     * @override
     */
    protected function traitementInsert(string $table, ?array $champs) : ?int {
        switch ($table) {
            case "commandedocument" :
                return $this->insertOneCommandeDocument($table, $champs);
            case "abonnement" :
                return $this->insertOneAbonnement($table, $champs);
            default:
                // cas général
                return $this->insertOneTupleOneTable($table, $champs);
        }
    }
    
    /**
     * demande de modification (update)
     * @param string $table
     * @param string|null $id
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples modifiés ou null si erreur
     * @override
     */
    protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int {
        switch($table) {
            case "" :
                //return $this->updateCommandeDocument($table, $id, $champs);
            default:
                // cas général
                return $this->updateOneTupleOneTable($table, $id, $champs);
        }
    }
    
    /**
     * demande de suppression (delete)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples supprimés ou null si erreur
     * @override
     */
    protected function traitementDelete(string $table, ?array $champs) : ?int {
        switch($table) {
            case "" :
                // return $this->uneFonction(parametres);
            default:
                // cas général
                return $this->deleteTuplesOneTable($table, $champs);
        }
    }

    /**
     * récupère les tuples d'une seule table
     * @param string $table
     * @param array|null $champs
     * @return array|null
     */
    private function selectTuplesOneTable(string $table, ?array $champs) : ?array {
        if (empty($champs)){
            // tous les tuples d'une table
            $requete = "select * from $table;";
            return $this->conn->queryBDD($requete);
        }else{
            // tuples spécifiques d'une table
            $requete = "select * from $table where ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key and ";
            }
            // (enlève le dernier and)
            $requete = substr($requete, 0, strlen($requete)-5);
            return $this->conn->queryBDD($requete, $champs);
        }
    }

    /**
    * demande d'ajout (insert) d'un tuple dans la table commandedocument
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */
    private function insertOneCommandeDocument(string $table, ?array $champs) : ?int {
        if (empty($champs)){
            echo $table;
            return null;
        }
        // Insérer une commande
        $requete = "insert into commande(id, datecommande, montant) ";
        $requete .= "values (:id, :datecommande, :montant);";
        $param = ["id" => $champs["Id"],
            "datecommande" => $champs["DateCommande"],
            "montant" => $champs["Montant"]
        ];
        $this->conn->updateBDD($requete, $param);
        // Insérer une commandedocumment
        $requete = "insert into commandedocument(id, nbexemplaire, idlivredvd, idsuivi) ";
        $requete .= "values(:id, :nbexemplaire, :idlivredvd, :idsuivi);";
        $param = ["id" => $champs["Id"],
            "nbexemplaire" => $champs["NbExemplaire"],
            "idlivredvd" => $champs["IdLivreDVD"],
            "idsuivi" => $champs["IdSuivi"]
        ];
        return $this->conn->updateBDD($requete, $param);
    }

    /**
     * demande d'ajout (insert) d'un tuple dans la table abonnement
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */
    private function insertOneAbonnement(string $table, ?array $champs) : ?int {
        if(empty($champs)) {
            echo $table;
            return null;
        }
        // Insérer une commande
        $requete = "insert into commande(id, datecommande, montant) ";
        $requete .= "values (:id, :datecommande, :montant);";
        $param = ["id" => $champs["Id"],
            "datecommande" => $champs["DateCommande"],
            "montant" => $champs["Montant"]
        ];
        $this->conn->updateBDD($requete, $param);
        // Insérer un abonnement
        $requete = "insert into abonnement(id, datefinabonnement, idrevue) ";
        $requete .= "values(:id, :datefinabonnement, :idrevue);";
        $param = ["id" => $champs["Id"],
            "datefinabonnement" => $champs["DateFinAbonnement"],
            "idrevue" => $champs["IdRevue"]
        ];
        return $this->conn->updateBDD($requete, $param);
    }

    /**
     * demande d'ajout (insert) d'un tuple dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */
    private function insertOneTupleOneTable(string $table, ?array $champs) : ?int {
        if (empty($champs)) {
            return null;
        }
        // construction de la requête
        $requete = "insert into $table (";
        foreach ($champs as $key => $value) {
            $requete .= "$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ") values (";
        foreach ($champs as $key => $value) {
            $requete .= ":$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ");";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * demande de modification (update) d'un tuple dans une table
     * @param string $table
     * @param string\null $id
     * @param array|null $champs
     * @return int|null nombre de tuples modifiés (0 ou 1) ou null si erreur
     */
    private function updateOneTupleOneTable(string $table, ?string $id, ?array $champs) : ?int {
        if (empty($champs)){
            return null;
        }
        if (is_null($id)){
            return null;
        }
        // construction de la requête
        $requete = "update $table set ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $champs["id"] = $id;
        $requete .= " where id=:id;";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * demande de suppression (delete) d'un ou plusieurs tuples dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples supprimés ou null si erreur
     */
    private function deleteTuplesOneTable(string $table, ?array $champs) : ?int {
        if (empty($champs)) {
            return null;
        }
        // construction de la requête
        $requete = "delete from $table where ";
        foreach ($champs as $key => $value) {
            $requete .= "$key=:$key and ";
        }
        // (enlève le dernier and)
        $requete = substr($requete, 0, strlen($requete)-5);
        return $this->conn->updateBDD($requete, $champs);
    }
 
    /**
     * récupère toutes les lignes d'une table simple (qui contient juste id et libelle)
     * @param string $table
     * @return array|null
     */
    private function selectTableSimple(string $table) : ?array {
        $requete = "select * from $table order by libelle;";
        return $this->conn->queryBDD($requete);
    }

    /**
     * récupère toutes les lignes de la table Livre et les tables associées
     * @return array|null
     */
    private function selectAllLivres() : ?array {
        $requete = "Select l.id, l.ISBN, l.auteur, d.titre, d.image, l.collection, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from livre l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }

    /**
     * récupère toutes les lignes de la table DVD et les tables associées
     * @return array|null
     */
    private function selectAllDvd() : ?array {
        $requete = "Select l.id, l.duree, l.realisateur, d.titre, d.image, l.synopsis, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from dvd l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }

    /**
     * récupère toutes les lignes de la table Revue et les tables associées
     * @return array|null
     */
    private function selectAllRevues() : ?array {
        $requete = "Select l.id, l.periodicite, d.titre, d.image, l.delaiMiseADispo, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from revue l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }

    /**
     * récupère tous les exemplaires d'une revue
     * @param array|null $champs
     * @return array|null
     */
    private function selectExemplairesRevue(?array $champs) : ?array {
        if (empty($champs)){
            return null;
        }
        if (!array_key_exists('id', $champs)) {
            return null;
        }
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select e.id, e.numero, e.dateAchat, e.photo, e.idEtat ";
        $requete .= "from exemplaire e join document d on e.id=d.id ";
        $requete .= "where e.id = :id ";
        $requete .= "order by e.dateAchat DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }
 
    /**
     * récupère toutes les lignes de la table CommandeDocument et les tables associées
     * @return array|null
     */
    private function selectAllCommandesDocument() : ?array {
        $requete = "Select c.id, c.datecommande, c.montant, cd.nbexemplaire, cd.idsuivi, s.libelle , cd.idlivredvd ";
        $requete .= "from commandedocument cd join commande c on cd.id=c.id ";
        $requete .= "join suivi s on s.id=cd.idsuivi ";
        $requete .= "order by id";
        return $this->conn->queryBDD($requete);
    }
    
    /**
     * récupère toutes les lignes de la table Abonnement et les tables associées
     * @return array|null
     */
    private function selectAllAbonnement() : ?array {
        $requete = "Select c.id, c.datecommande, c.montant, a.datefinabonnement, a.idrevue ";
        $requete .= "from abonnement a join commande c on a.id=c.id ";
        $requete .= "order by id";
        return $this->conn->queryBDD($requete);
    }
    
}

