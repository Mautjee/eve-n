<?php
/**
 * Over Ons — team member cards.
 *
 * Each card is a name, a portrait and a biography held together as one unit —
 * structure the block editor has no way to express for just two people. Two
 * is not enough to justify a custom post type, so this is a plain data
 * function until the team grows past that (worth revisiting then).
 *
 * @package eve-n
 */

defined( 'ABSPATH' ) || exit;

/**
 * The Eve-n team, in the order they appear on the Over Ons page.
 *
 * Bios are verbatim from reference/catalog-over-ons.md, one array entry per
 * paragraph so the template can print them as separate <p> tags.
 *
 * @return array[] Each entry: name, image (seeded-image key, see bin/seed.php), alt, bio (paragraphs).
 */
function even_team_members() {
	return array(
		array(
			'name'  => 'Eveline Hinfelaar',
			'image' => 'portrait-eveline',
			'alt'   => __( 'Portret van Eveline Hinfelaar, oprichter van Eve-n', 'eve-n' ),
			'bio'   => array(
				'Mijn naam is Eveline Hinfelaar. Al jarenlang ben ik gespecialiseerd in samenwerking binnen de infrastructuur. Als coach en adviseur geniet ik ervan om samen met organisaties en de teams daarbinnen aan de slag te gaan in het vormen van een effectief, verbonden en professioneel samenwerkend geheel.',
				"Naast mijn werk als adviseur en coach werk ik momenteel aan mijn promotieonderzoek (PhD) aan de Universiteit Twente. Met mijn onderzoek hoop ik organisaties binnen de infrastructuur te inspireren om op een bewustere, slimmere en duurzamere manier samen te werken binnen seriematige programma's.",
				'Ik woon in Rotterdam en ben moeder van twee volwassen kinderen. In alles wat ik doe staan verbinding, ontwikkeling en plezier in samenwerken centraal.',
			),
		),
		array(
			'name'  => 'Thomas Vilain',
			'image' => 'portrait-thomas',
			'alt'   => __( 'Portret van Thomas Vilain, teamlid bij Eve-n', 'eve-n' ),
			'bio'   => array(
				'Mijn naam is Thomas Vilain. Ik werk inmiddels drie jaar bij Eve-n en volg daarnaast een masteropleiding Culture, Organization and Management aan de Vrije Universiteit Amsterdam. Naast het mooie werk dat ik bij Eve-n mag doen, houd ik me graag bezig met sporten(voornamelijk boksen) en reizen. Wat ik zo leuk vind aan mijn werk is de afwisseling: geen dag is hetzelfde en ik leer continu nieuwe dingen.',
			),
		),
	);
}
