<?php

namespace TheIconic\NameParser\Language;

use TheIconic\NameParser\LanguageInterface;

/**
 * These lists have been extensively supplemented by
 * @author Volker Püschel <kuffy@anasco.de>
 * @copyright 2019 Volker Püschel
 * @license MIT
 *
 * The only structured definition of name parts and their allowed values
 * I could found on the web are according to HL7 (health level 7) standard
 * https://simplifier.net/guide/LeitfadenBasisDE/PatientimVersichertenstammdatenmanagementVSDM
 *
 * Names in Germany could be structured like this:
 * "[SALUTATION] [TITLES] [FIRSTNAME] [MIDDLENAMES] [EXTENSION] [LASTNAME_PREFIX] [LASTNAME], [SUFFIX]"
 *        1          n         1            n            1              1             1          n
 * source: https://wiki.hl7.de/index.php?title=bp:Personennamen
 *
 * Example:
 * "Herr Prof. Dr. med. Dr. rer. nat. Fritz Julius Karl Freiherr von und zu Rathenburg vor der Isar, MdB"
 * Where LASTNAME (FAMILYNAME/SURNAME) is "Rathenburg vor der Isar"
 * It is rather difficult to parse this name correctly, because "vor der" in this case
 * is part of the LASTNAME, but can also be a defined LASTNAME PREFIX!
 *
 * Derived from the standard for sorting (DIN 5007-2),
 * the name can also be written in this way (listings):
 * "Rathenburg vor der Isar, Fritz Julius Karl Freiherr von und zu"
 */

class German implements LanguageInterface
{
    const SUFFIXES = [
        '1.' => '1.',
        '2.' => '2.',
        '3.' => '3.',
        '4.' => '4.',
        '5.' => '5.',
        'i' => 'I',
        'ii' => 'II',
        'iii' => 'III',
        'iv' => 'IV',
        'v' => 'V',
        'jr.' => 'Jr.',
        'junior' => 'Junior',
        'senior' => 'Senior',
        'sr.' => 'Sr.',
        'der ältere' => 'der Ältere',
        'd. ä.' => 'd. Ä.',
        'd.ä.' => 'd.Ä.',
        'der jüngere' => 'der Jüngere',
        'd. j.' => 'd. J.',
        'd.j.' => 'd.J.',
    ];

    const SALUTATIONS = [
        'herr' => 'Herr',
        'hr' => 'Herr',
        'frau' => 'Frau',
        'fr' => 'Frau',
    ];

    /**
      * the following list is according to HL7 (Health Level 7) standard
      * https://www.vdek.com/vertragspartner/arbeitgeber/deuev/_jcr_content/par/download_8/file.res/Anlage_07_Vers.pdf
      */
    const LASTNAME_PREFIXES = [
        'a' => 'a',
        'aan de' => 'aan de',
        'aan den' => 'aan den',
        'al' => 'al',
        'am' => 'am',
        'an' => 'an',
        'an der' => 'an der',
        'auf' => 'auf',
        'auf dem' => 'auf dem',
        'auf der' => 'auf der',
        'auf m' => 'auf m',
        'aufm' => 'aufm',
        'auff m' => 'auff m',
        'aus' => 'aus',
        'aus dem' => 'aus dem',
        'aus den' => 'aus den',
        'aus der' => 'aus der',
        'b' => 'b',
        'be' => 'be',
        'bei' => 'bei',
        'bei der' => 'bei der',
        'beim' => 'beim',
        'ben' => 'ben',
        'bey' => 'bey',
        'bey der' => 'bey der',
        'che' => 'che',
        'cid' => 'cid',
        'd' => 'd',
        'd.' => 'd.',
        "d'" => "d'",
        'da' => 'da',
        'da costa' => 'da costa',
        'da las' => 'da las',
        'da silva' => 'da silva',
        'dal' => 'dal',
        'dall' => 'dall',
        "dall'" => "dall'",
        'dalla' => 'dalla',
        'dalle' => 'dalle',
        'dallo' => 'dallo',
        'das' => 'das',
        'de' => 'de',
        'degli' => 'degli',
        'dei' => 'dei',
        'den' => 'den',
        "de l '" => "de l '",
        'de la' => 'de la',
        'de las' => 'de las',
        'de le' => 'de le',
        'de los' => 'de los',
        'del' => 'del',
        'del coz' => 'del coz',
        'deli' => 'deli',
        'dell' => 'dell',
        "dell'" => "dell'",
        'della' => 'della',
        'delle' => 'delle',
        'delli' => 'delli',
        'dello' => 'dello',
        'der' => 'der',
        'des' => 'des',
        'di' => 'di',
        'dit' => 'dit',
        'do' => 'do',
        'do ceu' => 'do ceu',
        'don' => 'don',
        'don le' => 'don le',
        'dos' => 'dos',
        'dos santos' => 'dos santos',
        'du' => 'du',
        'dy' => 'dy',
        'el' => 'el',
        'g' => 'g',
        'gen' => 'gen',
        'gil' => 'gil',
        'gli' => 'gli',
        'grosse' => 'grosse',
        'groãÿe' => 'groÃŸe',
        'i' => 'i',
        'im' => 'im',
        'in' => 'in',
        'in de' => 'in de',
        'in den' => 'in den',
        'in der' => 'in der',
        'in het' => 'in het',
        "in't" => "in't",
        'kl' => 'kl',
        'kleine' => 'kleine',
        'l' => 'l',
        'l.' => 'l.',
        "l'" => "l'",
        'la' => 'la',
        'le' => 'le',
        'lee' => 'lee',
        'li' => 'li',
        'lo' => 'lo',
        'm' => 'm',
        'mc' => 'mc',
        'mac' => 'mac',
        'n' => 'n',
        'o' => 'o',
        "o'" => "o'",
        'op' => 'op',
        'op de' => 'op de',
        'op den' => 'op den',
        'op gen' => 'op gen',
        'op het' => 'op het',
        'op te' => 'op te',
        'op ten' => 'op ten',
        'oude' => 'oude',
        'pla' => 'pla',
        'pro' => 'pro',
        's' => 's',
        'st.' => 'st.',
        't' => 't',
        'te' => 'te',
        'ten' => 'ten',
        'ter' => 'ter',
        'thi' => 'thi',
        'tho' => 'tho',
        'thom' => 'thom',
        'thor' => 'thor',
        'thum' => 'thum',
        'to' => 'to',
        'tom' => 'tom',
        'tor' => 'tor',
        'tu' => 'tu',
        'tum' => 'tum',
        'unten' => 'unten',
        'unter' => 'unter',
        'unterm' => 'unterm',
        'v.' => 'v.',
        'v. d.' => 'v. d.',
        'v. dem' => 'v. dem',
        'v. den' => 'v. den',
        'v. der' => 'v. der',
        'v.d.' => 'v.d.',
        'v.dem' => 'v.dem',
        'v.den' => 'v.den',
        'v.der' => 'v.der',
        'van' => 'van',
        'van de' => 'van de',
        'van dem' => 'van dem',
        'van den' => 'van den',
        'van der' => 'van der',
        'vande' => 'vande',
        'vandem' => 'vandem',
        'vanden' => 'vanden',
        'vander' => 'vander',
        'van gen' => 'van gen',
        'van het' => 'van het',
        'van t' => 'van t',
        'ven' => 'ven',
        'ven der' => 'ven der',
        'ver' => 'ver',
        'vo' => 'vo',
        'vom' => 'vom',
        'vom und zu' => 'vom und zu',
        'von' => 'von',
        'von und zu' => 'von und zu',
        'von und zu der' => 'von und zu der',
        'von und zur' => 'von und zur',
        'von de' => 'von de',
        'von dem' => 'von dem',
        'von den' => 'von den',
        'von der' => 'von der',
        'von la' => 'von la',
        'von zu' => 'von zu',
        'von zum' => 'von zum',
        'von zur' => 'von zur',
        'vonde' => 'vonde',
        'vonden' => 'vonden',
        'vondem' => 'vondem',
        'vonder' => 'vonder',
        'von einem' => 'von einem',
        'von mast' => 'von mast',
        'vor' => 'vor',
        'vor dem' => 'vor dem',
        'vor den' => 'vor den',
        'vor der' => 'vor der',
        'vorm' => 'vorm',
        'vorn' => 'vorn',
        'y' => 'y',
        'y del' => 'y del',
        'zu' => 'zu',
        'zum' => 'zum',
        'zur' => 'zur',
    ];

    /**
      * the following list is according to HL7 (Health Level 7) standard
      * https://www.vdek.com/vertragspartner/arbeitgeber/deuev/_jcr_content/par/download_8/file.res/Anlage_07_Vers.pdf
      */
    const EXTENSIONS = [                            // nobility predicate (Adelsprädikate)
        'bar' => 'Bar',
        'baron' => 'Baron',
        'baroness' => 'Baroness',
        'baronesse' => 'Baronesse',
        'baronin' => 'Baronin',
        'brand' => 'Brand',
        'burggraf' => 'Burggraf',
        'burggräfin' => 'Burggräfin',
        'condesa' => 'Condesa',
        'earl' => 'Earl',
        'edle' => 'Edle',
        'edler' => 'Edler',
        'erbgraf' => 'Erbgraf',
        'erbgräfin' => 'Erbgräfin',
        'erbprinz' => 'Erbprinz',
        'erbprinzessin' => 'Erbprinzessin',
        'ffr' => 'Ffr',
        'freifr' => 'Freifr',
        'freifräulein' => 'Freifräulein',
        'freifrau' => 'Freifrau',
        'freih' => 'Freih',
        'freiherr' => 'Freiherr',
        'freiin' => 'Freiin',
        'frf' => 'Frf',
        'frf.' => 'Frf.',
        'frfr' => 'Frfr',
        'frfr.' => 'Frfr.',
        'frh' => 'Frh',
        'frh.' => 'Frh.',
        'frhr' => 'Frhr',
        'frhr.' => 'Frhr.',
        'fst' => 'Fst',
        'fst.' => 'Fst.',
        'fstn' => 'Fstn',
        'fstn.' => 'Fstn.',
        'fürst' => 'Fürst',
        'fürstin' => 'Fürstin',
        'gr' => 'Gr',
        'graf' => 'Graf',
        'gräfin' => 'Gräfin',
        'grf' => 'Grf',
        'grfn' => 'Grfn',
        'grossherzog' => 'Grossherzog',
        'großherzog' => 'Großherzog',
        'grossherzogin' => 'Grossherzogin',
        'großherzogin' => 'Großherzogin',
        'herzog' => 'Herzog',
        'herzogin' => 'Herzogin',
        'jhr' => 'Jhr',
        'jhr.' => 'Jhr.',
        'jonkheer' => 'Jonkheer',
        'junker' => 'Junker',
        'landgraf' => 'Landgraf',
        'landgräfin' => 'Landgräfin',
        'markgraf' => 'Markgraf',
        'markgräfin' => 'Markgräfin',
        'marques' => 'Marques',
        'marquis' => 'Marquis',
        'marschall' => 'Marschall',
        'ostoja' => 'Ostoja',
        'prinz' => 'Prinz',
        'prinzessin' => 'Prinzessin',
        'przin' => 'Przin',
        'rabe' => 'Rabe',
        'reichsgraf' => 'Reichsgraf',
        'reichsgräfin' => 'Reichsgräfin',
        'ritter' => 'Ritter',
        'rr' => 'Rr',
        'truchsess' => 'Truchsess',
        'truchseß' => 'Truchseß',
    ];

    /**
     * the following list contains the academic titles for doctor degrees
     * from DACH (Germany, Austria, Swiss)
     * copied from wikipedia (https://de.wikipedia.org/wiki/Doktor)
     */
    const TITLES_DR = [
        'ddr.' => 'DDr.',
        'dr.' => 'Dr.',
        'dr. e. h.' => 'Dr. E. h.',
        'dr.e.h.' => 'Dr.E.h.',
        'dr. ph' => 'Dr. PH',
        'dr.ph' => 'Dr.PH',
        'dr. sportwiss.' => 'Dr. Sportwiss.',
        'dr.sportwiss.' => 'Dr.Sportwiss.',
        'dr. agr.' => 'Dr. agr.',
        'dr.agr.' => 'Dr.agr.',
        'dr. biol.' => 'Dr. biol.',
        'dr.biol.' => 'Dr.biol.',
        'dr. cult.' => 'Dr. cult.',
        'dr.cult.' => 'Dr.cult.',
        'dr. des.' => 'Dr. des.',
        'dr.des.' => 'Dr.des.',
        'dr. diac.' => 'Dr. diac.',
        'dr.diac.' => 'Dr.diac.',
        'dr. disc. pol.' => 'Dr. disc. pol.',
        'dr.disc.pol.' => 'Dr.disc.pol.',
        'dr. e. h.' => 'Dr. e. h.',
        'dr.e.h.' => 'Dr.e.h.',
        'dr. eh.' => 'Dr. eh.',
        'dr.eh.' => 'Dr.eh.',
        'dr. h. c.' => 'Dr. h. c.',
        'dr.h.c.' => 'Dr.h.c.',
        'dr. h. c. mult.' => 'Dr. h. c. mult.',
        'dr.h.c.mult.' => 'Dr.h.c.mult.',
        'dr. habil.' => 'Dr. habil.',
        'dr.habil.' => 'Dr.habil.',
        'dr. iur.' => 'Dr. iur.',
        'dr.iur.' => 'Dr.iur.',
        'dr. iur. can.' => 'Dr. iur. can.',
        'dr.iur.can.' => 'Dr.iur.can.',
        'dr. iur. et rer. pol.' => 'Dr. iur. et rer. pol.',
        'dr.iur.etrer.pol.' => 'Dr.iur.etrer.pol.',
        'dr. iur. utr.' => 'Dr. iur. utr.',
        'dr.iur.utr.' => 'Dr.iur.utr.',
        'dr. math.' => 'Dr. math.',
        'dr.math.' => 'Dr.math.',
        'dr. med.' => 'Dr. med.',
        'dr.med.' => 'Dr.med.',
        'dr. med. dent.' => 'Dr. med. dent.',
        'dr.med.dent.' => 'Dr.med.dent.',
        'dr. med. dent. sci.' => 'Dr. med. dent. sci.',
        'dr.med.dent.sci.' => 'Dr.med.dent.sci.',
        'dr. med. sci.' => 'Dr. med. sci.',
        'dr.med.sci.' => 'Dr.med.sci.',
        'dr. med. univ.' => 'Dr. med. univ.',
        'dr.med.univ.' => 'Dr.med.univ.',
        'dr. med. univ. et scient. med.' => 'Dr. med. univ. et scient. med.',
        'dr.med.univ.etscient.med.' => 'Dr.med.univ.etscient.med.',
        'dr. med. vet.' => 'Dr. med. vet.',
        'dr.med.vet.' => 'Dr.med.vet.',
        'dr. mont.' => 'Dr. mont.',
        'dr.mont.' => 'Dr.mont.',
        'dr. mult.' => 'Dr. mult.',
        'dr.mult.' => 'Dr.mult.',
        'dr. nat. med.' => 'Dr. nat. med.',
        'dr.nat.med.' => 'Dr.nat.med.',
        'dr. nat. oec.' => 'Dr. nat. oec.',
        'dr.nat.oec.' => 'Dr.nat.oec.',
        'dr. nat. techn.' => 'Dr. nat. techn.',
        'dr.nat.techn.' => 'Dr.nat.techn.',
        'dr. oec.' => 'Dr. oec.',
        'dr.oec.' => 'Dr.oec.',
        'dr. oec. hsg' => 'Dr. oec. HSG',
        'dr.oec.hsg' => 'Dr.oec.HSG',
        'dr. oec. publ.' => 'Dr. oec. publ.',
        'dr.oec.publ.' => 'Dr.oec.publ.',
        'dr. oec. troph.' => 'Dr. oec. troph.',
        'dr.oec.troph.' => 'Dr.oec.troph.',
        'dr. paed.' => 'Dr. paed.',
        'dr.paed.' => 'Dr.paed.',
        'dr. pharm.' => 'Dr. pharm.',
        'dr.pharm.' => 'Dr.pharm.',
        'dr. phil.' => 'Dr. phil.',
        'dr.phil.' => 'Dr.phil.',
        'dr. phil. fac. theol.' => 'Dr. phil. fac. theol.',
        'dr.phil.fac.theol.' => 'Dr.phil.fac.theol.',
        'dr. phil. in art.' => 'Dr. phil. in art.',
        'dr.phil.inart.' => 'Dr.phil.inart.',
        'dr. phil. nat.' => 'Dr. phil. nat.',
        'dr.phil.nat.' => 'Dr.phil.nat.',
        'dr. rer. agr.' => 'Dr. rer. agr.',
        'dr.rer.agr.' => 'Dr.rer.agr.',
        'dr. rer. biol. hum.' => 'Dr. rer. biol. hum.',
        'dr.rer.biol.hum.' => 'Dr.rer.biol.hum.',
        'dr. rer. biol. vet.' => 'Dr. rer. biol. vet.',
        'dr.rer.biol.vet.' => 'Dr.rer.biol.vet.',
        'dr. rer. cam.' => 'Dr. rer. cam.',
        'dr.rer.cam.' => 'Dr.rer.cam.',
        'dr. rer. comm.' => 'Dr. rer. comm.',
        'dr.rer.comm.' => 'Dr.rer.comm.',
        'dr. rer. cult.' => 'Dr. rer. cult.',
        'dr.rer.cult.' => 'Dr.rer.cult.',
        'dr. rer. cur.' => 'Dr. rer. cur.',
        'dr.rer.cur.' => 'Dr.rer.cur.',
        'dr. rer. forest.' => 'Dr. rer. forest.',
        'dr.rer.forest.' => 'Dr.rer.forest.',
        'dr. rer. hort.' => 'Dr. rer. hort.',
        'dr.rer.hort.' => 'Dr.rer.hort.',
        'dr. rer. hum.' => 'Dr. rer. hum.',
        'dr.rer.hum.' => 'Dr.rer.hum.',
        'dr. rer. med.' => 'Dr. rer. med.',
        'dr.rer.med.' => 'Dr.rer.med.',
        'dr. rer. medic.' => 'Dr. rer. medic.',
        'dr.rer.medic.' => 'Dr.rer.medic.',
        'dr. rer. merc.' => 'Dr. rer. merc.',
        'dr.rer.merc.' => 'Dr.rer.merc.',
        'dr. rer. mil.' => 'Dr. rer. mil.',
        'dr.rer.mil.' => 'Dr.rer.mil.',
        'dr. rer. mont.' => 'Dr. rer. mont.',
        'dr.rer.mont.' => 'Dr.rer.mont.',
        'dr. rer. nat.' => 'Dr. rer. nat.',
        'dr.rer.nat.' => 'Dr.rer.nat.',
        'dr. rer. oec.' => 'Dr. rer. oec.',
        'dr.rer.oec.' => 'Dr.rer.oec.',
        'dr. rer. physiol.' => 'Dr. rer. physiol.',
        'dr.rer.physiol.' => 'Dr.rer.physiol.',
        'dr. rer. pol.' => 'Dr. rer. pol.',
        'dr.rer.pol.' => 'Dr.rer.pol.',
        'dr. rer. publ.' => 'Dr. rer. publ.',
        'dr.rer.publ.' => 'Dr.rer.publ.',
        'dr. rer. publ. hsg' => 'Dr. rer. publ. HSG',
        'dr.rer.publ.hsg' => 'Dr.rer.publ.HSG',
        'dr. rer. rel.' => 'Dr. rer. rel.',
        'dr.rer.rel.' => 'Dr.rer.rel.',
        'dr. rer. sec.' => 'Dr. rer. sec.',
        'dr.rer.sec.' => 'Dr.rer.sec.',
        'dr. rer. silv.' => 'Dr. rer. silv.',
        'dr.rer.silv.' => 'Dr.rer.silv.',
        'dr. rer. soc.' => 'Dr. rer. soc.',
        'dr.rer.soc.' => 'Dr.rer.soc.',
        'dr. rer. soc. oec.' => 'Dr. rer. soc. oec.',
        'dr.rer.soc.oec.' => 'Dr.rer.soc.oec.',
        'dr. rer. tech.' => 'Dr. rer. tech.',
        'dr.rer.tech.' => 'Dr.rer.tech.',
        'dr. sc.' => 'Dr. sc.',
        'dr.sc.' => 'Dr.sc.',
        'dr. sc. eth zürich' => 'Dr. sc. ETH Zürich',
        'dr.sc.eth zürich' => 'Dr.sc.ETH Zürich',
        'dr. sc. agr.' => 'Dr. sc. agr.',
        'dr.sc.agr.' => 'Dr.sc.agr.',
        'dr. sc. hum.' => 'Dr. sc. hum.',
        'dr.sc.hum.' => 'Dr.sc.hum.',
        'dr. sc. inf.' => 'Dr. sc. inf.',
        'dr.sc.inf.' => 'Dr.sc.inf.',
        'dr. sc. inf. biomed.' => 'Dr. sc. inf. biomed.',
        'dr.sc.inf.biomed.' => 'Dr.sc.inf.biomed.',
        'dr. sc. inf. med.' => 'Dr. sc. inf. med.',
        'dr.sc.inf.med.' => 'Dr.sc.inf.med.',
        'dr. sc. math.' => 'Dr. sc. math.',
        'dr.sc.math.' => 'Dr.sc.math.',
        'dr. sc. med.' => 'Dr. sc. med.',
        'dr.sc.med.' => 'Dr.sc.med.',
        'dr. sc. mus.' => 'Dr. sc. mus.',
        'dr.sc.mus.' => 'Dr.sc.mus.',
        'dr. sc. nat.' => 'Dr. sc. nat.',
        'dr.sc.nat.' => 'Dr.sc.nat.',
        'dr. sc. oec.' => 'Dr. sc. oec.',
        'dr.sc.oec.' => 'Dr.sc.oec.',
        'dr. sc. pol.' => 'Dr. sc. pol.',
        'dr.sc.pol.' => 'Dr.sc.pol.',
        'dr. sc. rel.' => 'Dr. sc. rel.',
        'dr.sc.rel.' => 'Dr.sc.rel.',
        'dr. sc. soc.' => 'Dr. sc. soc.',
        'dr.sc.soc.' => 'Dr.sc.soc.',
        'dr. sc. techn.' => 'Dr. sc. techn.',
        'dr.sc.techn.' => 'Dr.sc.techn.',
        'dr. scient. med' => 'Dr. scient. med',
        'dr.scient.med' => 'Dr.scient.med',
        'dr. techn.' => 'Dr. techn.',
        'dr.techn.' => 'Dr.techn.',
        'dr. theol.' => 'Dr. theol.',
        'dr.theol.' => 'Dr.theol.',
        'dr. troph.' => 'Dr. troph.',
        'dr.troph.' => 'Dr.troph.',
        'dr.-ing.' => 'Dr.-Ing.',
        'ph. d.' => 'Ph. D.',
        'ph.d.' => 'Ph.D.',
    ];

    /**
     * the following list contains the academaic titles mainly for professors
     * from DACH (Germany, Austria, Swiss), which are often used in names as titles
     * copied from wikipedia (https://de.wikipedia.org/wiki/Professor)
     * this list is kept separate from TITLES to better maintain both
     */
    const OFFICIAL_TITLES = [
        'ao. univ.-prof.' => 'ao. Univ.-Prof.',
        'ao.univ.-prof.' => 'ao.Univ.-Prof.',
        'apl. prof.' => 'apl. Prof.',
        'apl.prof.' => 'apl.Prof.',
        'ass.-prof.' => 'Ass.-Prof.',
        'assoz. prof.' => 'assoz. Prof.',
        'assoz.prof.' => 'assoz.Prof.',
        'hon.-prof.' => 'Hon.-Prof.',
        'jun.-prof.' => 'Jun.-Prof.',
        'o. univ.-prof.' => 'o. Univ.-Prof.',
        'o.univ.-prof.' => 'o.Univ.-Prof.',
        'o.ö. prof.' => 'o.ö. Prof.',
        'o.ö.prof.' => 'o.ö.Prof.',
        'pd' => 'PD',
        'priv.-doz.' => 'Priv.-Doz.',
        'prof. em.' => 'Prof. em.',
        'prof. emer.' => 'Prof. emer.',
        'prof. h. c.' => 'Prof. h. c.',
        'prof. hon.' => 'Prof. hon.',
        'prof. i. k.' => 'Prof. i. K.',
        'prof.' => 'Prof.',
        'prof.em.' => 'Prof.em.',
        'prof.emer.' => 'Prof.emer.',
        'prof.h.c.' => 'Prof.h.c.',
        'prof.hon.' => 'Prof.hon.',
        'prof.i.k.' => 'Prof.i.K.',
        'professor' => 'Professor',
        'professorin' => 'Professorin',
        'tit. prof.' => 'Tit. Prof.',
        'tit.prof.' => 'Tit.Prof.',
        'univ.-prof.' => 'Univ.-Prof.',
    ];

    const JOB_TITLES = [
        'dipl.-ing.' => 'Dipl.-Ing.',
        'ra' => 'RA',
    ];

    const COMPANIES = [
        ' - ' => ' - ',
        '& co.' => '& Co.',
        '&' => '&',
        '+' => '+',
        'ag & co. kg' => 'AG & Co. KG',
        'ag & co. kgaa' => 'AG & Co. KGaA',
        'ag' => 'AG',
        'agentur' => 'Agentur',
        'aktien' => 'Aktien',
        'apotheke' => 'Apotheke',
        'bank' => 'Bank',
        'bkk' => 'BKK',
        'büro' => 'büro',
        'centrum' => 'Centrum',
        'dienst' => 'Dienst',
        'direct' => 'Direct',
        'direkt' => 'Direkt',
        'e. g.' => 'e. G.',
        'e. k.' => 'e. K.',
        'e. kfm' => 'e. Kfm',
        'e. kfr' => 'e. Kfr',
        'e.g.' => 'e.G.',
        'e.k.' => 'e.K.',
        'e.v.' => 'e.V.',
        'eg' => 'eG',
        'eingetragene kauffrau' => 'eingetragene Kauffrau',
        'eingetragener kaufmann' => 'eingetragener Kaufmann',
        'elektro' => 'Elektro',
        'finanz' => 'Finanz',
        'friseur' => 'Friseur',
        'frisier' => 'Frisier',
        'gbr' => 'GbR',
        'gen.' => 'Gen.',
        'genossenschaft' => 'Genossenschaft',
        'ges. m. b. h.' => 'Ges. m. b. H.',
        'ges.' => 'Ges.',
        'ges.m.b.h.' => 'Ges.m.b.H.',
        'gesellschaft' => 'Gesellschaft',
        'ggmbh' => 'gGmbH',
        'gmbh & co. kg' => 'GmbH & Co. KG',
        'gmbh & co. kgaa' => 'GmbH & Co. KGaA',
        'gmbh & co. ohg' => 'GmbH & Co. OHG',
        'gmbh' => 'GmbH',
        'gymnasium' => 'Gymnasium',
        'hotel' => 'Hotel',
        'i. g.' => 'i. G.',
        'i.g.' => 'i.G.',
        'kg' => 'KG',
        'kgaa' => 'KGaA',
        'krankenhaus' => 'Krankenhaus',
        'krankenkasse' => 'Krankenkasse',
        'llc & co. kg' => 'LLC & Co. KG',
        'mbh' => 'mbH',
        'ohg' => 'OHG',
        'polizei' => 'Polizei',
        'praxis' => 'Praxis',
        'restaurant' => 'Restaurant',
        'salon' => 'salon',
        'schule' => 'Schule',
        ' se' => ' SE',
        'service' => 'Service',
        'steuer' => 'Steuer',
        'stiftung' => 'Stiftung',
        'technik' => 'Technik',
        'theater' => 'Theater',
        'ug (haftungsbeschränkt)' => 'UG (haftungsbeschränkt)',
        'und co. kg' => 'und Co. KG',
        'und co.' => 'und Co.',
        'universität' => 'Universität',
        'unternehmergesellschaft (haftungsbeschränkt)' => 'Unternehmergesellschaft (haftungsbeschränkt)',
        'verband' => 'Verband',
        'verein' => 'Verein',
        'versicherung' => 'Versicherung',
        'verwaltung' => 'Verwaltung',
        'vvag' => 'VVaG',
        'zentrum' => 'Zentrum',
    ];

    public function getSuffixes(): array
    {
        return self::SUFFIXES;
    }

    public function getSalutations(): array
    {
        return self::SALUTATIONS;
    }

    public function getLastnamePrefixes(): array
    {
        return self::LASTNAME_PREFIXES;
    }

    public function getExtensions(): array
    {
        return self::EXTENSIONS;
    }

    public function getTitles(): array
    {
        return array_merge(self::TITLES_DR, self::OFFICIAL_TITLES, self::JOB_TITLES);
    }

    public function getCompanies(): array
    {
        return self::COMPANIES;
    }
}
