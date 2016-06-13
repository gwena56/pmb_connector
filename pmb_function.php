<?php
//get_option('pmbc_dbuser'),get_option('pmbc_dbpwd'), get_option('pmbc_dbname'), get_option('pmbc_dbhost')
function connect(){ // connection préparation UT8
    $servername = get_option('pmbc_dbhost');
    $username = get_option('pmbc_dbuser');
    $password = get_option('pmbc_dbpwd');
    //connection to the database
    $dbpmb = mysql_connect($servername, $username, $password) or die("Unable to connect to server.");
    //select a database to work with
    $selected = mysql_select_db(get_option('pmbc_dbname'),$dbpmb) or die("SQL error : database not found.");
    mysql_query ("set character_set_client='utf8'"); 
    mysql_query ("set character_set_results='utf8'"); 
    return $dbpmb;
}
function close($dbpmb){
    mysql_close($dbpmb);
    }

function pmbGetNotice($notice_id) { // -> trouver UNE notice en fonction de son id : retour Notice pmb
    $dbpmb = connect();
    $result = mysql_query("
    SELECT *
    FROM  `notices` 
    WHERE  notice_id=$notice_id;
    ",$dbpmb);
    $notice = mysql_fetch_object($result);
    //close the connection
    close($dbpmb);
    return $notice;
}
function Localisation(){ // recup libelles localisation PMB
    $dbpmb = connect();
    $result = mysql_query("
        SELECT idlocation, location_libelle, location_visible_opac
        FROM docs_location
        ",$dbpmb);
    while ($notice = mysql_fetch_object($result)) {
        $retour[$notice->idlocation] = $notice;
        }
    close($dbpmb);
    return $retour;
}
function Statut(){ // recup Libelles Statut PMB
    $dbpmb = connect();
    $result = mysql_query("
        SELECT idstatut, statut_libelle_opac, statut_visible_opac
        FROM docs_statut
        ",$dbpmb);
    while ($notice = mysql_fetch_object($result)) {
        $retour[$notice->idstatut] = $notice;
        }
    close($dbpmb);
    return $retour;
}
function Section(){ // recup Libelles Section PMB
    $dbpmb = connect();
    $result = mysql_query("
        SELECT idsection, section_libelle, section_visible_opac
        FROM docs_section
        ",$dbpmb);
    while ($notice = mysql_fetch_object($result)) {
        $retour[$notice->idsection] = $notice;
    }
    close($dbpmb);
    return $retour;
}
function Typedoc(){ // recup Libelles Type de doc PMB
    $dbpmb = connect();
    $result = mysql_query("
        SELECT idtyp_doc, tdoc_libelle
        FROM docs_type
        ",$dbpmb);
    while ($notice = mysql_fetch_object($result)) {
        $retour[$notice->idtyp_doc] = $notice;
    }
    close($dbpmb);
    return $retour;
}
function Exemplaires($id){ // return une array pas un object - pour l'instant.
    $docs_loc = Localisation(); //Recupération des intitule code pmb pour Locaisation Statut et Section
    $docs_statut = Statut();
    $docs_sect = Section();
    $docs_type = Typedoc();
    $dbpmb = connect();

    $result = mysql_query("
        SELECT exemplaires.expl_id, exemplaires.expl_cb, exemplaires.expl_typdoc, exemplaires.expl_cote, exemplaires.expl_section,exemplaires.expl_statut, exemplaires.expl_location, pret.pret_idexpl,resa.resa_idnotice
        FROM  `exemplaires`
        LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl
        LEFT JOIN resa ON exemplaires.expl_notice=resa.resa_idnotice
        WHERE  `expl_notice` =$id;
        ",$dbpmb);
    $retour = array();
    $idx = 0;
    while ($notice = mysql_fetch_object($result)) {
        $retour[$idx] = $notice; // le paquet puis quelques modifs sur la nouvelle fiche exemplaire
        $sec = $docs_sect[(int)$notice->expl_section]->section_libelle;
        $notice->expl_section = $sec;
        $stat = $docs_statut[(int)$notice->expl_statut]->statut_libelle_opac;
        $notice->expl_statut= $stat;
        $loc = $docs_loc[(int)$notice->expl_location]->location_libelle;
        $notice->expl_location= $loc;
        $typ = $docs_type[(int)$notice->expl_typdoc]->tdoc_libelle;
        $notice->expl_typdoc_l = $typ;
        $idx ++;
    }
    //close the connection
    close($dbpmb);
    return $retour;
}
function Editeurs($ed) {
    $dbpmb = connect();
    $sqlEditeur = "select ed_id,ed_name,ed_adr1,ed_ville from publishers where ed_id='$ed';";
    $editeur = mysql_query($sqlEditeur);
    $ee = mysql_fetch_object($editeur);
    return $ee;
    close($dbpmb);
}
function Auteurs($notice) {
    $dbpmb = connect();
    $sqlAuthors = "select author_id, responsability_type, author_name, author_rejete, author_date from responsability, authors where responsability_notice=$notice";
    $sqlAuthors .= " and responsability_author=author_id"; //and responsability_type = 0
    $authors = mysql_query($sqlAuthors,$dbpmb);
    $aa = mysql_fetch_object($authors);
    return (object) $aa;
    close($dbpmb);
}
function Thumbnail($notice){
    //http://46.18.97.83/opac_css/images/no_image.jpg
    $object = array();
    $opac = get_option('pmbc_opac');
    $tp = pmbGetNotice($notice);
    $img_url = $opac."/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F%21%21isbn%21%21.08.MZZZZZZZ.jpg&noticecode=";
    $isbn_notice =  $tp->code;
    $isbn_notice = str_replace('-', '', $isbn_notice);
    if ($isbn_notice != '') {
        $img_url .= $img_url . $isbn_notice;
        $img_url .= '&vigurl=';
        $img_url .= urlencode($tp->thumbnail_url); 
        $img_url .= '';
    }
    else
    { 
        $img_url = $opac."/images/no_image.jpg";
    }
    $object['html'] = '"<img src="' . $img_url . '" style="display: inline; height: 50%; margin-bottom: 10px; margin-left: 33%;" </img>';
    $object['url'] = $img_url;
    return (object) $object;
}
function WPpagelink($notice){
    $urlWP = get_option('pmbc_notice');
    return $urlWP.'/?notice='.$notice->notice_id.'" title="'.$notice->tit1;
}
function htmlNoticeSimple($object){
    $urlWP = get_option('pmbc_notice');
    $tmp = "";
    $tmp .= '<div align="center" !!STYLE!!>';
    $tmp .= '<a href="'.$urlWP.'/?notice='.$object['ID'].'" title="'.$object['TITRE'].'">';
    $tmp .= '<img src="'.$object['URL'].'" >';
    $tmp .= '</a><hr />';
    $tmp .= ''.$object['TITRE'].'<br>';
    //$tmp .= ''.$object['EDITEUR'].'<br>';
    $tmp .= '</div>';
    return $tmp;
}
function htmlNotice($object){
    $tmp = "";
    $tmp .= '<table style="width: 100%;" border="0" cellpadding="3" cellspacing="3">';
    $tmp .= '<tbody><tr>';
    $tmp .= '<td style="text-align: center; width: 22%;"><img src="'.$object['URL'].'" ></td>';
    $tmp .= '<td style="text-align: left; vertical-align: top;"><h1>'.$object['TITRE'].'</h1><br><b>Auteur : </b>'.$object['AUTEUR'].'<br>';
    $tmp .= '<hr style="color: grey; border-style: solid; margin-left: 0px; margin-right: auto;">';
    $tmp .= '<b>ISBN : </b>'.$object['ISBN'].'<br>';
    $tmp .= '<b>Editeur : </b>'.$object['EDITEUR'].'<br>';
    $tmp .= $object['PAGES'].' - '.$object['ANNEE'].'<br>';
    $tmp .= '</td>';
    $tmp .= '</tr><tr>';
    $tmp .= '<td>'.$object['er'].'</td>'; /* nuage de mots categories */
    $tmp .= '<td>'.htmlExemplaire($object).'</td>';
    $tmp .= '</tr>';
    $tmp .= '</tbody></table>';
    return $tmp;
}
function htmlExemplaire($object){
    //$idx = $object['NB_EXPL'];
    $enr = $object['EXPL'];
    $tmp .= '<ul>';
    for($i = 0; $i < count($enr); ++$i) {
        $tmp .= '<li>';
        $tmp .= $enr[$i]->expl_cb.' - '.$enr[$i]->expl_cote;
        if ($enr[$i]->pret_idexpl === NULL) { $tmp .= " - <b> Disponible </b>";} else { $tmp .= " - <b> en prêt </b>";} 
        if ($enr[$i]->resa_idnotice === NULL) { $tmp .= "";} else { $tmp .= " - <b> déjà réservé </b>";}    
        $tmp .= '</li>';
    }
    $tmp .= '</ul>';
    return $tmp;
}
function htmlCarousel($ftype,$fnb,$fvalue,$fparams){ 
    //sql fnb notices aux hasard dans la bu
    switch ($ftype) {
        case 'cote': // par le début de la cote
            $sql = "
            SELECT notices.notice_id, notices.tit1, notices.code, notices.opac_visible_bulletinage,exemplaires.expl_cote
            FROM notices
            JOIN exemplaires ON exemplaires.expl_notice = notices.notice_id 
            WHERE code <> '' and notices.opac_visible_bulletinage='1'
            AND exemplaires.expl_cote LIKE '$fvalue%'
            ORDER BY RAND() DESC LIMIT $fnb;
        ";
            break;
        case 'comgestion': // utiliser commentaire de gestion pour spécifier des particularités de votre fonctionnement par exemple : acheté par tel prof, 
            $sql = "
            SELECT notices.notice_id, notices.tit1, notices.code, notices.opac_visible_bulletinage, notices.commentaire_gestion
            FROM notices
            JOIN exemplaires ON exemplaires.expl_notice = notices.notice_id 
            WHERE code <> '' and notices.opac_visible_bulletinage='1'
            AND notices.commentaire_gestion LIKE '%$fvalue%'
            ORDER BY RAND() DESC LIMIT $fnb;
        ";        
            break;
        case 'categories': // par un mot catégorie matiere un mot unique
            $sql = "
            SELECT notices.notice_id, notices.tit1, notices.code, notices.opac_visible_bulletinage, notices.index_matieres
            FROM notices
            JOIN exemplaires ON exemplaires.expl_notice = notices.notice_id 
            WHERE code <> '' and notices.opac_visible_bulletinage='1'
            AND notices.index_matieres LIKE '%$fvalue%'
            ORDER BY RAND() DESC LIMIT $fnb;
        ";
            break;
        case 'sections': // par le nom de la section de la bibliothèque ex 9 - Histoire doit correspondre parfaitement
            $sec = Section(); //recuperation du nom des sections
            $sql = "
            SELECT notices.notice_id, notices.tit1, notices.code, notices.opac_visible_bulletinage,exemplaires.expl_section,docs_section.section_libelle
            FROM notices
            JOIN exemplaires ON exemplaires.expl_notice = notices.notice_id
            LEFT JOIN docs_section ON docs_section.idsection = exemplaires.expl_section 
            WHERE code <> '' and notices.opac_visible_bulletinage='1'
            AND docs_section.section_libelle = '$fvalue'
            ORDER BY RAND() DESC LIMIT $fnb;
        ";            
            break;
        default: // default basic dans toute la BU
            $sql = "
            SELECT notice_id, tit1, code, opac_visible_bulletinage
            FROM  `notices` 
            WHERE code <> '' and opac_visible_bulletinage='1'
            ORDER BY RAND() DESC LIMIT $fnb;
        ";
            break;
    }
    
    $dbpmb = connect();
    $result = mysql_query($sql,$dbpmb);
    //close the connection
    // du html top: 0px; right: -75px; width: 600px; height: 200px; div data-p="112.50" style="display: none;"
    $ttp =
<<<EOD
    <div id="jssor_1" style="background-repeat:no-repeat;!!CONTENER!!">
        <div data-u="slides" style="cursor: default; position: absolute; overflow: hidden;!!SLIDE!!">
EOD;
// la bouboucle qui affiche les nonotitices
    while ($notice = mysql_fetch_object($result)) {
        $img_url=Thumbnail($notice->notice_id)->url;
        $link=WPpagelink($notice);
        $ttp .=
<<<EOD
            <div>
                <a href="$link">
                <img alt="$notice->tit1" data-u="image" style="box-shadow: 3px 3px 3px black;" src="$img_url" />
                </a>
            </div>
EOD;
        }
    close($dbpmb);

    $ttp .=
<<<EOD
        </div>
        <!-- Bullet Navigator -->
        <div data-u="navigator" class="jssorb05" style="bottom:16px;right:16px;" data-autocenter="1">
        <!-- bullet navigator item prototype -->
            <div data-u="prototype" style="width:16px;height:16px;"></div>
        </div>
        <!-- Arrow Navigator -->
        <span data-u="arrowleft" class="jssora12l" style="!!ARROWLEFT!!" data-autocenter="4"></span>
        <span data-u="arrowright" class="jssora12r" style="!!ARROWRIGHT!!" data-autocenter="4"></span>
        </div>
        <script>
            jssor_1_slider_init(150,200);
        </script>
EOD;
    return $ttp; 
}
function pmbGetRandomNews($date_min,$nb=1){
    $dbpmb = connect();
    $result = mysql_query("
        SELECT notices.notice_id,notices.code, notices.opac_visible_bulletinage, notices.create_date, notices.niveau_biblio, exemplaires.expl_location, exemplaires.expl_notice, exemplaires.expl_typdoc
        FROM notices
        JOIN exemplaires ON exemplaires.expl_notice = notices.notice_id 
        WHERE notices.create_date >= '$date_min'
        AND notices.code <> '' and notices.opac_visible_bulletinage='1'
        AND notices.niveau_biblio = 'm'
        AND exemplaires.expl_location = 1
        AND exemplaires.expl_typdoc = '1'
    ;
    ",$dbpmb);
    $tab = array();
    while ($notice = mysql_fetch_row($result)) {
        array_push($tab, $notice);
    }
    close($dbpmb);
    //var_dump($tab);
    return $tab[array_rand($tab,$nb)][0];
}
function pmbGetParam($type_param,$param) {
    $dbpmb = connect();
    $result = mysql_query("
    SELECT id_param,valeur_param,section_param FROM parametres WHERE type_param='$type_param' AND sstype_param='$param';
        ");
    close($dbpmb);
    return mysql_fetch_object($result);
}
function pmbLoad($fret,$ftype,$fvalue) {
    //pmbLoad('html','id',58878);
    /* FUnction
    Flag Retour : Html ou Object
    Flag Type : id - le déclencheur est id de la notice PMB Value = integer
            isbn - le déclencheur est l'isbn de la notice PMB (utile pour des recherches sur WEB) value est String
            code - le déclencheur est le numéro code barre de l'exemplaire value string

    */
    $object = array();
    switch ($ftype) {
        case 'id':
            $tp0 = pmbGetNotice($fvalue);
            $tp1 = Thumbnail($tp0->notice_id);
            $tp2 = Auteurs($tp0->notice_id);
            $tp3 = Editeurs($tp0->ed1_id);
            $tp4 = Exemplaires($tp0->notice_id);
    //  TRaitement des exemplaires
            $object['ID'] = $tp0->notice_id;
            $object['OPAC'] = $tp0->opac_visible_bulletinage;
            $object['TITRE']= $tp0->tit1;
            $object['ISBN'] = $tp0->code;
            $object['URL'] = $tp1->url;
            $object['AUTEUR']= $tp2->author_name . ", " . $tp2->author_rejete . " (" . $tp2->author_date . ") "; // quand il y a plusieurs ?
            $object['EDITEUR'] = $tp3->ed_name . "(" . $tp3->ed_ville . ")";
            $object['ANNEE'] = $tp0->year;
            $object['PAGES'] = $tp0->npages;
            //$object['NB_EXPL']=sizeof($tp4); pas utile
            $object['EXPL'] = $tp4;
            $object['er']="";
            break;
        case 'isbn':
            echo "pmbLoad case isbn";
            exit;
        default:
            $object['er']="<FTYPE ERROR>";
            break;
    }
    switch ($fret) {
        case 'object':
            return (object) $object;
            break;
        case 'notice': // sert à afficher une notice de pmb dans les pages et articles de Wordpress = Html complexe
            echo htmlNotice($object);
            break;
        default:
            # du HTML FORMAT DIV // Notice format html div = Html Simple
            return htmlNoticeSimple($object);
            break;
    }
}
?>