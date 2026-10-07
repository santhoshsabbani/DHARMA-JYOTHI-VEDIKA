<?php
/**
 * DJV Core — Canonical Pooja Guides Dataset
 *
 * Contains 12 comprehensive traditional Pooja Vidhanam guides,
 * with full 12-step canonical ritual progression, preparation, samagri,
 * trilingual content (English, Telugu, Hindi), authentic Vedic Sankalpam,
 * traditional variations disclaimer, and direct relationships to Mantras and Festivals.
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Universal 15-Step "How to Start a Hindu Puja" overview guide.
 */
function djv_get_generic_how_to_start_puja(): array {
	return [
		'disclaimer_en' => 'Procedure may vary according to family tradition, sampradaya and regional practice.',
		'disclaimer_te' => 'పూజా విధానం కుటుంబ ఆచారం, సాంప్రదాయం మరియు ప్రాంతీయ నియమాల ప్రకారం మారవచ్చు.',
		'disclaimer_hi' => 'पूजा की विधि पारिवारिक परम्परा, सम्प्रदाय और क्षेत्रीय रीति-रिवाजों के अनुसार भिन्न हो सकती है।',
		'steps' => [
			[
				'step'     => 1,
				'title_en' => 'Clean the Puja Sthalam',
				'title_te' => 'పూజా స్థలాన్ని శుభ్రపరచడం',
				'title_hi' => 'पूजा स्थल की शुद्धि',
				'desc_en'  => 'Sweep and mop the altar area with clean water or gangajal. Draw auspicious muggulu (rangoli) using rice powder.',
				'desc_te'  => 'పూజా గదిని లేదా పీఠాన్ని శుద్ధ జలంతో లేదా గంగాజలంతో తుడిచి, బియ్యప్పిండితో పవిత్రమైన ముగ్గు వేయండి.',
				'desc_hi'  => 'पूजा स्थल को स्वच्छ जल अथवा गंगाजल से धोकर पवित्र करें और चावल के आटे से मांगलिक रंगोली बनाएं।'
			],
			[
				'step'     => 2,
				'title_en' => 'Bathe and Wear Clean Clothes',
				'title_te' => 'స్నానం మరియు శుభ్రమైన వస్త్రధారణ',
				'title_hi' => 'स्नान एवं स्वच्छ वस्त्र धारण',
				'desc_en'  => 'Take a purifying bath and wear clean, unstitched or traditional garments (dhoti/kurta or saree). Apply tilak.',
				'desc_te'  => 'శుచిగా తలస్నానం చేసి సాంప్రదాయ వస్త్రాలు ధరించి, లలాటాన తిలకం (కుంకుమ/విభూతి/చందనం) ధరించండి.',
				'desc_hi'  => 'शुद्ध जल से स्नान कर धुले हुए पारंपरिक वस्त्र धारण करें और मस्तक पर तिलक लगाएं।'
			],
			[
				'step'     => 3,
				'title_en' => 'Prepare the Puja Samagri',
				'title_te' => 'పూజా సామగ్రిని సిద్ధం చేసుకోవడం',
				'title_hi' => 'पूजा सामग्री की तैयारी',
				'desc_en'  => 'Arrange lamps, ghee, wicks, fresh flowers, akshatas, panchamritam, incense, and naivedyam on separate pure plates.',
				'desc_te'  => 'దీపాలు, ఆవు నెయ్యి, వత్తులు, పూలు, అక్షతలు, పంచామృతాలు, ధూపం మరియు నైవేద్యాన్ని పవిత్ర పాత్రల్లో అమర్చుకోండి.',
				'desc_hi'  => 'दीपक, घी, बत्तियां, ताजे पुष्प, अक्षत, पंचामृत, धूप, नैवेद्य आदि सामग्री को शुद्ध थाली में सजाएं।'
			],
			[
				'step'     => 4,
				'title_en' => 'Place the Deity (Asanam)',
				'title_te' => 'దేవతామూర్తి ప్రతిష్ఠ / ఆసనం',
				'title_hi' => 'देव प्रतिमा/चित्र की स्थापना',
				'desc_en'  => 'Place a wooden peetham covered with red or yellow cloth. Consecrate the idol or framed portrait facing East or North.',
				'desc_te'  => 'చెక్క పీటపై ఎరుపు లేదా పసుపు వస్త్రం పరచి, తూర్పు లేదా ఉత్తర దిశకు అభిముఖంగా దేవతా విగ్రహాన్ని/చిత్రపటాన్ని ఉంచండి.',
				'desc_hi'  => 'लकड़ी की चौकी पर लाल या पीला वस्त्र बिछाकर पूर्व या उत्तर दिशा की ओर मुख करके भगवान की प्रतिमा या चित्र स्थापित करें।'
			],
			[
				'step'     => 5,
				'title_en' => 'Light the Lamp (Deeparadhana)',
				'title_te' => 'దీపారాధన',
				'title_hi' => 'दीप प्रज्वलन',
				'desc_en'  => 'Light the brass or silver oil lamps using pure cow ghee or sesame oil with two wicks. Pray to the Divine Light.',
				'desc_te'  => 'ఆవు నెయ్యి లేదా నువ్వుల నూనెతో రెండు వత్తులు వేసి దీపారాధన చేయండి. "దీపజ్యోతిః పరబ్రహ్మ" అని ప్రార్థించండి.',
				'desc_hi'  => 'गाय के शुद्ध घी या तिल के तेल से दो बत्तियों वाला दीपक जलाएं और "दीपज्योतिः परब्रह्म" का स्मरण करें।'
			],
			[
				'step'     => 6,
				'title_en' => 'Light Incense (Dhoopam)',
				'title_te' => 'ధూపం వెలిగించడం',
				'title_hi' => 'धूप प्रज्वलन',
				'desc_en'  => 'Light natural dhoop or agarbatti to sanctify the atmosphere and invoke divine vibrations.',
				'desc_te'  => 'సుగంధ ధూపం లేదా అగరుబత్తులను వెలిగించి పూజా గదిలో ఆధ్యాత్మిక పరిమళాన్ని నింపండి.',
				'desc_hi'  => 'सुगंधित धूप या अगरबत्ती जलाकर वातावरण को पवित्र और सुगंधित करें।'
			],
			[
				'step'     => 7,
				'title_en' => 'Perform Achamana (Purification)',
				'title_te' => 'ఆచమనం',
				'title_hi' => 'आचमन एवं शुद्धि',
				'desc_en'  => 'Sip three drops of water from the right palm chanting Keshavaya Namah, Narayanaya Namah, Madhavaya Namah.',
				'desc_te'  => 'ఉద్ధరిణెతో మూడుమార్లు కుడి చేతిలో తీర్థం తీసుకుని కేశవ, నారాయణ, మాధవ నామాలతో ఆచమనం చేయండి.',
				'desc_hi'  => 'दाहिनी हथेली में तीन बार जल लेकर "ॐ केशवाय नमः, ॐ नारायणाय नमः, ॐ माधवाय नमः" बोलकर आचमन करें।'
			],
			[
				'step'     => 8,
				'title_en' => 'Take Sankalpa (Solemn Resolve)',
				'title_te' => 'సంకల్పం',
				'title_hi' => 'संकल्प',
				'desc_en'  => 'Hold water, akshatas, and a coin in your right hand. State the location, tithi, your gotra, name, and spiritual intent.',
				'desc_te'  => 'కుడిచేతిలో అక్షతలు, పువ్వు, నాణెం పట్టుకుని దేశకాల తిథి వారాలు, గోత్ర నామాలు మరియు కోరికను సంకల్పించుకోండి.',
				'desc_hi'  => 'दाहिने हाथ में जल, अक्षत और सिक्का लेकर स्थान, तिथि, नक्षत्र, अपना गोत्र, नाम और पूजा का संकल्प लें।'
			],
			[
				'step'     => 9,
				'title_en' => 'Invoke the Deity (Avahanam & Prana Pratishtha)',
				'title_te' => 'ఆవాహనం మరియు ప్రాణ ప్రతిష్ఠ',
				'title_hi' => 'आवाहन एवं ध्यान',
				'desc_en'  => 'Chant the Dhyana Shlokas with folded hands, inviting the divine presence of the Lord into the consecrated idol.',
				'desc_te'  => 'చేతులు జోడించి ధ్యాన శ్లోకాలు పఠిస్తూ భగవంతుని దివ్య చైతన్యాన్ని పూజా పీఠంపైకి ఆహ్వానించండి.',
				'desc_hi'  => 'हाथ जोड़कर ध्यान श्लोकों का पाठ करते हुए प्रभु के पावन स्वरूप का अपने हृदय व प्रतिमा में आह्वान करें।'
			],
			[
				'step'     => 10,
				'title_en' => 'Perform Prescribed Puja (Shodashopachara)',
				'title_te' => 'షోడశోపచార పూజ',
				'title_hi' => 'षोडशोपचार पूजा',
				'desc_en'  => 'Offer Padya, Arghya, Snana, Vastra, Gandha, Pushpa, and Archana with 108 Ashtottara names of the deity.',
				'desc_te'  => 'పాద్యం, అర్ఘ్యం, స్నానం, వస్త్రం, గంధం, పూలు సమర్పించి 108 అష్టోత్తర శతనామావళితో అక్షతలు పూలు సమర్పించండి.',
				'desc_hi'  => 'पाद्य, अर्घ्य, आचमन, स्नान, वस्त्र, गंध, पुष्प अर्पित कर 108 अष्टोत्तर शतनामावली से अर्चना करें।'
			],
			[
				'step'     => 11,
				'title_en' => 'Chant Associated Mantra (Japa)',
				'title_te' => 'మంత్ర జపం / స్తోత్ర పఠనం',
				'title_hi' => 'मंत्र जप एवं स्तोत्र पाठ',
				'desc_en'  => 'Recite the consecrated Moola Mantra, Gayatri Mantra, or Kavacham associated with the deity using a Japa Mala.',
				'desc_te'  => 'తులసి లేదా రుద్రాక్ష మాలతో ఆయా దేవతా మూల మంత్రం లేదా గాయత్రీ మంత్రాన్ని 108 సార్లు భక్తితో జపించండి.',
				'desc_hi'  => 'रुद्राक्ष या तुलसी माला से इष्टदेव के मूल मंत्र अथवा गायत्री मंत्र का कम से कम 108 बार एकाग्रता से जप करें।'
			],
			[
				'step'     => 12,
				'title_en' => 'Offer Naivedyam',
				'title_te' => 'నైవేద్య సమర్పణ',
				'title_hi' => 'नैवेद्य अर्पण',
				'desc_en'  => 'Sprinkle water with Tulasi or flower around the food offering. Chant the Amritopastaranamasi mantras.',
				'desc_te'  => 'తాజా ప్రసాదాన్ని లేదా పండ్లను భగవంతుని ముందుంచి తులసి దళంతో జలం చల్లుతూ నైవేద్యం సమర్పించండి.',
				'desc_hi'  => 'तुलसी दल अथवा पुष्प से भोग के चारों ओर जल छिड़क कर श्रद्धापूर्वक नैवेद्य अर्पित करें।'
			],
			[
				'step'     => 13,
				'title_en' => 'Perform Mangala Aarti (Karpura Neerajanam)',
				'title_te' => 'మంగళ హారతి (కర్పూర నీరాజనం)',
				'title_hi' => 'आरती एवं कर्पूर नीराजन',
				'desc_en'  => 'Light pure camphor or ghee wicks. Wave the Aarti clockwise while ringing the bell and singing devotional hymns.',
				'desc_te'  => 'ఘంటానాదం చేస్తూ కర్పూరం లేదా నేతి హారతిని సవ్యదిశలో తిప్పుతూ భక్తి గీతాలు లేదా మంగళ హారతి పాడండి.',
				'desc_hi'  => 'घंटी बजाते हुए कपूर या घी की बत्तियों से दक्षिणावर्त आरती घुमाएं और भावपूर्ण आरती गायन करें।'
			],
			[
				'step'     => 14,
				'title_en' => 'Offer Prarthana & Pradakshina (Prayer & Circumambulation)',
				'title_te' => 'ప్రార్థన మరియు ఆత్మప్రదక్షిణ',
				'title_hi' => 'प्रार्थना एवं प्रदक्षिणा',
				'desc_en'  => 'Turn clockwise 3 times reciting "Yani Kani Cha Papani". Prostrate fully (Sashtanga Namaskaram) and seek forgiveness.',
				'desc_te'  => '"యానికాని చ పాపాని" అంటూ 3 సార్లు ఆత్మప్రదక్షిణ చేసి, సాష్టాంగ నమస్కారం ఆచరించి క్షమాపణ వేడుకోండి.',
				'desc_hi'  => 'तीन बार अपने स्थान पर प्रदक्षिणा करें, साष्टांग प्रणाम कर क्षमा याचना करें और कल्याण की प्रार्थना करें।'
			],
			[
				'step'     => 15,
				'title_en' => 'Take Theertham & Prasadam',
				'title_te' => 'తీర్థ ప్రసాద స్వీకరణ',
				'title_hi' => 'चरणामृत एवं प्रसाद ग्रहण',
				'desc_en'  => 'Accept the consecrated Theertham and Prasadam with devotion, and distribute to all family members and devotees.',
				'desc_te'  => 'పూజా తీర్థాన్ని కళ్ళకద్దుకుని స్వీకరించి, ప్రసాదాన్ని కుటుంబ సభ్యులకు, భక్తులకు పంచిపెట్టండి.',
				'desc_hi'  => 'श्रद्धापूर्वक चरणामृत और प्रसाद ग्रहण करें तथा परिवार व उपस्थित सभी भक्तों में वितरित करें।'
			]
		]
	];
}

/**
 * Returns all 12 Canonical Pooja Guides.
 */
function djv_get_canonical_poojas(): array {
	return [
		// ═══════════════════════════════════════════════════════════
		// 1. MAHA GANAPATI POOJA (Vinayaka Chavithi / Prathama Puja)
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'ganapati-pooja',
			'title'           => 'Maha Ganapati Pooja (మహా గణపతి పూజా విధానం)',
			'title_en'        => 'Maha Ganapati Pooja Vidhanam',
			'title_te'        => 'మహా గణపతి పూజా విధానం',
			'title_hi'        => 'महा गणपति पूजा विधान',
			'deity'           => 'Ganesha',
			'deity_slug'      => 'ganesha',
			'category'        => 'Deity Poojas',
			'duration'        => '45 Minutes',
			'intro_en'        => 'Lord Ganesha is the Prathama Vandya (first worshipped deity) in Sanatana Dharma. As Vighnaharta, He removes obstacles from any spiritual, domestic, or worldly endeavour. This traditional procedure follows the Shodashopachara Vidhanam including Haridra Ganapati (Turmeric Ganesha) invocation, 21-patri (sacred leaves) worship, modakam naivedyam, and Atharvashirsha recitation.',
			'intro_te'        => 'సనాతన ధర్మంలో సమస్త శుభకార్యాలకు, పూజలకు ప్రథమ పూజ్యుడు శ్రీ మహాగణపతి. ఏ పని ప్రారంభించినా విఘ్నాలు తొలగి నిర్విఘ్నంగా విజయవంతం కావడానికి పసుపు గణపతి పూజ మరియు షోడశోపచార గణపతి పూజను ఆచరిస్తారు. ఇందులో 21 రకాల పత్రాలతో ఏకవింశతి పత్రపూజ, మోదకాలు-కుడుముల నైవేద్యం, మంగళ హారతి విశేషాలు ఉంటాయి.',
			'intro_hi'        => 'सनातन धर्म में किसी भी शुभ कार्य, अनुष्ठान या नित्य पूजा में सर्वप्रथम विघ्नहर्ता भगवान श्री गणेश की आराधना की जाती है। इस पारंपरिक षोडशोपचार पूजा विधान में हरिद्रा गणपति की स्थापना, 21 दुर्वा-पत्र पूजन, मोदक नैवेद्य और अथर्वशीर्ष पाठ की प्रामाणिक विधि समाहित है।',
			'samagri'         => "• Turmeric powder (పసుపు / हल्दी) for Haridra Ganapati\n• Kumkum and Sandalwood paste (కుంకుమ, గంధం / कुमकुम, चंदन)\n• 21 blades of fresh Durva grass (గరిక / दुर्वा)\n• Red flowers and Hibiscus garland (ఎరుపు మందార పూలు / लाल गुड़हल)\n• 21 Modaks, Kudumulu, or Undrallu (మోదకాలు, ఉండ్రాళ్లు / मोदक, लड्डू)\n• Betel leaves and nuts (తమలపాకులు, పోకచెక్కలు / पान, सुपारी)\n• Coconut, Bananas, and Seasonal fruits (కొబ్బరికాయ, అరటిపండ్లు / नारियल, फल)\n• Pure cow ghee and cotton wicks for Deeparadhana\n• Camphor and brass bell for Aarti",
			'preparation'     => 'Sit facing East or North on an asana. Cleanse the puja mandapam, draw a swastika or lotus with rice flour, place a wooden peetham, and prepare a small cone of turmeric mixed with water as Haridra Ganapati placed on a betel leaf.',
			'sankalpam'       => 'मम उपात्त समस्त दुरितक्षयद्वारा श्री परमेश्वर प्रीत्यर्थं शुभे शोभने मुहूर्ते... श्री महागणपति प्रसाद सिद्ध्यर्थं निर्विघ्नता सिद्ध्यर्थं ध्यानावाहनादि षोडशोपचार पूजां करिष्ये ॥',
			'kalasha_sthapana' => 'Fill a copper or brass vessel with fresh water, put cardamom, clove, a coin, and betel leaf. Place mango twigs on mouth and top with coconut smeared with turmeric. Recite Kalasha Gayatri.',
			'avahanam'        => 'ॐ गं गणपतये नमः । आवाहयामि स्थापयामि पूजयामि । हे हेरम्ब, त्वमेहि अम्बिकासुत प्रसीद मे ॥ Invoke Lord Ganesha into the turmeric idol with folded hands and akshatas.',
			'dhyana'          => 'शुक्लाम्बरधरं विष्णुं शशिवर्णं चतुर्भुजम् । प्रसन्नवदनं ध्यायेत् सर्वविघ्नोपशान्तये ॥ गजाननं भूतगणादिसेवितं कपित्थजम्बूफलचारुभक्षणम् । उमासुतं शोकविनाशकारकं नमामि विघ्नेश्वरपादपङ्कजम् ॥',
			'main_puja'       => 'Offer Asanam, Padya, Arghya, Achamaniya, Snana with rose water and panchamritam, Vastra, Yajnopavita, Gandham, Kumkuma, and perform 21 Durva archana chanting the 21 sacred names: Sumukhaya Namah, Ekadantaya Namah, Kapilaya Namah...',
			'mantra_japa'     => 'Chant Om Gam Ganapataye Namah (ॐ गं गणपतये नमः) 108 times, followed by Sri Ganapati Atharvashirsha.',
			'naivedyam'       => 'Offer steamed Kudumulu, sweet Undrallu, Modakas, fresh jaggery, raw coconut, bananas, and honey. Circle water three times chanting Amritopastaranamasi.',
			'aarti'           => 'Light pure camphor and wave in clockwise circles singing "Jaya Ganesha Deva" or traditional Telugu "Sukhamu Shanti Pradasinchu Vinayaka". Ring the bell joyously.',
			'prarthana'       => 'विघ्नेश्वराय वरदाय सुरप्रियाय लम्बोदराय सकलाय जगद्धिताय । नागाननाय श्रुतियज्ञविभूषिताय गौरीसुताय गणनाथ नमो नमस्ते ॥',
			'prasadam'        => 'Distribute the blessed modakas, kudumulu, and coconut to all family members and visitors with reverence.',
			'visarjan'        => 'For daily turmeric Ganapati or temporary clay Ganesha: On the final day, sprinkle water with the mantra "यान्तु देवगणाः सर्वे पूजामादाय मामकीम्", move the idol slightly towards the North, and immerse respectfully in clean natural water.',
			'vrat_rules'      => 'Fast from sunrise until completing the midday Puja on Vinayaka Chavithi. Avoid seeing the moon on Bhadrapada Shukla Chavithi night (or chant the Syamantakopakhyanam story if viewed).',
			'faq'             => [
				[
					'q' => 'Why is Haridra (Turmeric) Ganapati made first before every puja?',
					'a' => 'Turmeric represents pure auspiciousness, health, and earth element. Consecrating Ganesha in turmeric removes all atmospheric doshas and ensures any subsequent ritual proceeds without hindrance.'
				],
				[
					'q' => 'Can we do this puja daily at home?',
					'a' => 'Yes. Daily puja can be simplified to lighting lamps, offering fresh flowers, chanting the 12 names (Sankata Nashana Stotra), and offering jaggery or fruit.'
				]
			],
			'related_mantras'  => [ 'ganesh-mantra', 'vakratunda-mahakaya', 'ganesh-gayatri-mantra', 'ganapati-atharvashirsha' ],
			'related_festivals'=> [ 'ganesh-chaturthi', 'ganesh-visarjan', 'sankashti-chaturthi' ],
			'seo_title_en'     => 'Maha Ganapati Pooja Vidhanam: Step-by-Step Procedure, Samagri & Mantras',
			'seo_title_te'     => 'మహా గణపతి పూజా విధానం: సంపూర్ణ షోడశోపచార పూజ, సామగ్రి, మంత్రాలు',
			'seo_title_hi'     => 'महा गणपति पूजा विधान: संपूर्ण षोडशोपचार विधि, सामग्री एवं मंत्र',
			'seo_desc_en'      => 'Complete step-by-step Vedic Ganapati Puja Vidhanam at home with Haridra Ganapati invocation, 21 Durva archana, Modak naivedyam, and Atharvashirsha at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో పసుపు గణపతి పూజ, షోడశోపచార పూజా విధానం, ఏకవింశతి పత్ర పూజ, నైవేద్యం మరియు గణేశ మంత్రాల సంపూర్ణ వివరణ.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर प्रामाणिक गणपति पूजा विधि, हल्दी गणेश स्थापना, 21 दुर्वा अर्चन, मोदक नैवेद्य और मंत्र जाप की संपूर्ण जानकारी।'
		],

		// ═══════════════════════════════════════════════════════════
		// 2. SRI SATYANARAYANA SWAMY POOJA
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'satyanarayana-pooja',
			'title'           => 'Sri Satyanarayana Swamy Pooja (సత్యనారాయణ వ్రతం)',
			'title_en'        => 'Sri Satyanarayana Swamy Pooja & Vratham',
			'title_te'        => 'శ్రీ సత్యనారాయణ స్వామి వ్రత విధానం',
			'title_hi'        => 'श्री सत्यनारायण स्वामी व्रत एवं पूजा विधि',
			'deity'           => 'Vishnu',
			'deity_slug'      => 'vishnu',
			'category'        => 'Vratam',
			'duration'        => '2.5 Hours',
			'intro_en'        => 'Sri Satyanarayana Swamy Vratham, detailed in the Reva Khanda of the Skanda Purana, is the preeminent household worship for family well-being, success in commerce, child blessings, and liberation from hardship. It is especially auspicious on Purnima, Ekadashi, and housewarming ceremonies.',
			'intro_te'        => 'స్కాంద పురాణంలోని రేవాఖండంలో సూత మహర్షి శౌనకాది మునులకు ఉపదేశించిన అత్యంత పుణ్యప్రదమైన వ్రతం శ్రీ సత్యనారాయణ స్వామి వ్రతం. గృహప్రవేశం, వివాహం, సకల కార్యానుకూలతకు మరియు పౌర్ణమి దినాల్లో ఈ వ్రతాన్ని ఆచరించడం వల్ల సమస్త ఇబ్బందులు తొలగి ఐశ్వర్యవంతులవుతారు.',
			'intro_hi'        => 'स्कन्द पुराण के रेवा खण्ड में वर्णित श्री सत्यनारायण व्रत कलिकाल में कष्ट निवारण, धन-धान्य वृद्धि और मनोकामना पूर्ति का सर्वश्रेष्ठ साधन है। पूर्णिमा, एकादशी या मांगलिक अवसरों पर पांच कथाओं के श्रवण और सपाद नैवेद्य अर्पण के साथ यह अनुष्ठान किया जाता है।',
			'samagri'         => "• Silver or Copper Kalash, Raw Rice (1 kg), Five mango leaves, Coconut\n• Red and yellow unstitched cloth for peetham\n• Turmeric, Kumkum, Sandalwood paste, Akshatas\n• Navadhanyalu (9 grains) for Navagraha mandapam\n• Sapada Prasadam ingredients: Wheat sooji (or rice flour), Pure cow ghee, Sugar or Jaggery, Cow milk, Ripe bananas (in equal measure 1¼ ratio)\n• Betel leaves (50), Supari nuts (25), Seasonal fruits, Flowers and Garlands\n• Camphor, Incense sticks, Panchamritam ingredients",
			'preparation'     => 'Decorate a wooden mandapam with plantain stems (ariti chetlu) at four corners. Form a bed of rice, draw an eight-petal lotus (Ashtadala Padma), consecrate the Kalash and install the golden/silver idol or portrait of Lord Satyanarayana.',
			'sankalpam'       => 'अद्य पूर्वोक्त गुणविशेषण विशिष्टायां शुभपुण्यतिथौ... मम कुटुम्बस्य क्षेमस्थैर्यधैर्यविजयाभयआयुरारोग्यैश्वर्याभिवृद्ध्यर्थं श्री सत्यनारायण स्वामी प्रीत्यर्थं यथाशक्ति पूजाहं करिष्ये ॥',
			'kalasha_sthapana' => 'Fill Kalash with pure water, coins, betel nut, scent. Place mango twigs and coconut on top. Wrap red thread (raksha sutra) around the Kalash neck.',
			'avahanam'        => 'ॐ नमो भगवते वासुदेवाय । सत्यनारायणं देवं वन्देऽहं कामदं प्रभुम् । लीलया विततं विश्वं येन तस्मै नमो नमः ॥ Invoke Lord Satyanarayana with Lakshmi Devi.',
			'dhyana'          => 'ध्यायेत्सत्यं गुणातीतं गुणवन्तं सनातनम् । जगदीश्वरं जगन्नाथं जगदाधारमच्युतम् ॥ शांतकारं भुजगशयनं पद्मनाभं सुरेशं विश्वाधारं गगनसदृशं मेघवर्णं शुभाङ्गम् ॥',
			'main_puja'       => 'Perform Ganapati Pooja, Varuna Pooja, Navagraha Pooja, Ashta Dikpalaka Pooja, followed by Shodashopachara Pooja to Lord Satyanarayana with Tulasi leaves and Vishnu Sahasranama archana.',
			'mantra_japa'     => 'Chant the Dwadashakshari Mantra: ॐ नमो भगवते वासुदेवाय (Om Namo Bhagavate Vasudevaya) 108 times, followed by Sri Satyanarayana Ashtottara Shatanama Stotram.',
			'naivedyam'       => 'Offer the sacred Sapada Bhakshya (Prasadam prepared with equal proportions of sooji/wheat, ghee, sugar/jaggery, milk, and chopped bananas). Recite the Naivedya mantras and offer Tulasi leaves.',
			'aarti'           => 'Wave camphor and ghee wicks while singing "Jai Jagadish Hare" or the Telugu Vratha Mangala Harati. All family members bow together.',
			'prarthana'       => 'कायेन वाचा मनसेन्द्रियैर्वा बुद्ध्यात्मना वा प्रकृतिस्वभावात् । करोमि यद्यत्सकलं परस्मै नारायणायेति समर्पयामि ॥',
			'prasadam'        => 'Carefully distribute the consecrated Sapada Prasadam to all guests and family members. It is traditional that no one leaves without tasting the prasadam.',
			'visarjan'        => 'After conclusion and Katha shravanam, respectfully move the Kalash slightly North chanting the Udvasana mantra, thanking the Lord for presiding over the home.',
			'vrat_rules'      => 'Maintain fasting or satvik milk-fruit diet until the completion of the 5 Vratha Kathas. Listen with full devotion and peace of mind.',
			'faq'             => [
				[
					'q' => 'What is the significance of the 5 stories (Kathas) in Satyanarayana Vratham?',
					'a' => 'The stories illustrate truthfulness (Satya), humility, keeping promises, and avoiding arrogance across all sections of society (Brahmana, King, Merchant, and Forest Dweller).'
				]
			],
			'related_mantras'  => [ 'om-namo-bhagavate-vasudevaya', 'vishnu-sahasranama', 'hare-krishna-mahamantra', 'lakshmi-mantra' ],
			'related_festivals'=> [ 'karthika-pournami', 'guru-purnima', 'vaikuntha-ekadashi' ],
			'seo_title_en'     => 'Sri Satyanarayana Swamy Pooja Vidhi & Vratham Procedure: Step-by-Step Guide',
			'seo_title_te'     => 'శ్రీ సత్యనారాయణ స్వామి వ్రత విధానం: సంపూర్ణ పూజా విధానం, కథలు, ప్రసాదం',
			'seo_title_hi'     => 'श्री सत्यनारायण स्वामी व्रत विधि: संपूर्ण पूजन प्रक्रिया, सपाद नैवेद्य एवं कथा',
			'seo_desc_en'      => 'Detailed Satyanarayana Swamy Vratham vidhi with Kalasha sthapana, Sapada Prasadam recipe, 5 Katha guidelines, and mantras at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో సత్యనారాయణ వ్రత సామగ్రి, కలశ స్థాపన, సపాద ప్రసాదం తయారీ, పూజా విధానం మరియు నారాయణ మంత్రాల సంపూర్ణ సమాచారం.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर श्री सत्यनारायण स्वामी व्रत की संपूर्ण शास्त्रीय विधि, कलश स्थापना, सपाद प्रसाद और कथा श्रवण के नियम पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 3. VARALAKSHMI VRATHAM
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'varalakshmi-vratham',
			'title'           => 'Varalakshmi Vratham (వరలక్ష్మీ వ్రతం)',
			'title_en'        => 'Varalakshmi Vratham Puja Vidhanam',
			'title_te'        => 'వరలక్ష్మీ వ్రత పూజా విధానం',
			'title_hi'        => 'वरलक्ष्मी व्रत एवं पूजा विधान',
			'deity'           => 'Lakshmi',
			'deity_slug'      => 'lakshmi',
			'category'        => 'Vratam',
			'duration'        => '2 Hours',
			'intro_en'        => 'Observed on the Friday preceding the full moon day of Sravana month, Varalakshmi Vratham invokes Goddess Lakshmi, the grantor of boons (Varam). Worshiping Varalakshmi is equivalent to worshiping Ashta Lakshmi—the eight embodiments of wealth, courage, wisdom, and progeny.',
			'intro_te'        => 'శ్రావణ మాసంలో పౌర్ణమికి ముందు వచ్చే శుక్రవారం నాడు ముత్తైదువులు అత్యంత భక్తిశ్రద్ధలతో ఆచరించే మహోన్నత వ్రతం శ్రీ వరలక్ష్మీ వ్రతం. ఈ వ్రతం ఆచరించడం వల్ల అష్టలక్ష్ముల అనుగ్రహం లభించి, సౌభాగ్యం, ఆయురారోగ్యాలు, సంతాన ప్రాప్తి మరియు సకల ఐశ్వర్యాలు చేకూరుతాయి.',
			'intro_hi'        => 'श्रावण मास की पूर्णिमा से पूर्व आने वाले शुक्रवार को सौभाग्यवती स्त्रियों द्वारा वरलक्ष्मी व्रत रखा जाता है। इस दिन वरदायिनी मां लक्ष्मी का अष्टलक्ष्मी स्वरूप में पूजन कर रक्षासूत्र (तोरम) बांधा जाता है और घर में सुख-समृद्धि का वास होता है।',
			'samagri'         => "• Silver or Brass Kalash, Coconut, Fresh Mango leaves\n• Yellow silk blouse piece or small saree for dressing the Kalash\n• Lakshmi silver/brass face mask or idol\n• Yellow thread with 9 knots (Nava Granthi Toram) smeared with turmeric\n• Nine varieties of flower offerings (Lotus, Jasmine, Marigold, etc.)\n• Nine varieties of Naivedyam (Pulihora, Garelu, Boorelu, Paramannam, Chalimidi, Vadapappu, Sweet Pongal, Sundal, Payasam)\n• Blouse pieces, bangles, turmeric, kumkum, and betel leaves for Vayanam distribution",
			'preparation'     => 'Clean home thoroughly. Draw muggulu. Set up wooden peetham with raw rice spread out. Fill Kalash, place mango twigs, set coconut smeared with turmeric and kumkum, attach Lakshmi Mukhadvara (face). Adorn with jewellery and flowers.',
			'sankalpam'       => 'मम गृहे अष्टलक्ष्मी प्रसादेन स्थिरलक्ष्मी सिद्ध्यर्थं, भर्तुः दीर्घायुष्यार्थं, पुत्रपौत्रादि अभिवृद्ध्यर्थं श्री वरమహాలక్ష్మీ దేవతా ప్రీత్యర్థం వరలక్ష్మీ వ్రతం కరిష్యే ॥',
			'kalasha_sthapana' => 'Consecrate the decorated Lakshmi Kalash on the rice spread reciting "कलशस्य मुखे विष्णुः कण्ठे रुद्रः समाश्रितः". Offer akshatas and flower petals.',
			'avahanam'        => 'ॐ श्रीं ह्रीं क्लीं श्रीं सिद्धलक्ष्म्यै नमः । सर्वमङ्गलमाङ्गल्ये शिवे सर्वार्थसाधिके । शरण्ये त्र्यम्बके गौरि नारायणि नमोऽस्तु ते ॥ Inviting Goddess Varalakshmi into the home.',
			'dhyana'          => 'पद्मानने पद्मविपद्मपत्रे पद्मप्रिये पद्मदलायताक्षि । विश्वप्रिये विश्वमनोऽनुकूले त्वत्पादपद्मं मयि संनिधत्स्व ॥',
			'main_puja'       => 'Perform Shodashopachara offerings, followed by Ashta Lakshmi Puja and Lakshmi Ashtottara Shatanama Archana with lotus flowers, kumkum, and akshatas. Tie the consecrated 9-knot yellow Toram onto the right wrist.',
			'mantra_japa'     => 'Chant the Mahalakshmi Moola Mantra: ॐ श्रीं महालक्ष्म्यै नमः (Om Shreem Mahalakshmyai Namah) 108 times, followed by Sri Suktam recitation.',
			'naivedyam'       => 'Offer the traditional nine varieties of dishes: Pulihora, Garelu, Boorelu, Paramannam, Chalimidi, Vadapappu, Fresh fruits, Coconut, and Panchamritam.',
			'aarti'           => 'Light Karpura Deepam and oil lamps. Sing "Ksheera Sagara Kanya" and "Bhagyada Lakshmi Baramma".',
			'prarthana'       => 'नमस्तेऽस्तु महामाये श्रीपीठे सुरपूजिते । शङ्खचक्रगदाहस्ते महालक्ष्मि नमोऽस्तु ते ॥',
			'prasadam'        => 'Consume the sanctified prasadam with family after offering Vayanam (traditional offering of blouse piece, fruits, betel leaves, turmeric, and bangles) to invited married women (Muttaiduvulu).',
			'visarjan'        => 'Next morning (Saturday) after performing punah-puja, respectfully move the Kalash slightly towards North and distribute the coconut water/rice.',
			'vrat_rules'      => 'Fast with water or fruits until the puja and toram tying are completed. Maintain auspicious thoughts and joy throughout the day.',
			'faq'             => [
				[
					'q' => 'What is the significance of the 9 knots on the Toram (Raksha Thread)?',
					'a' => 'The nine knots symbolize the nine manifestations of Goddess Lakshmi and the nine forms of wealth (Nidhi) protecting the family against all negative forces.'
				]
			],
			'related_mantras'  => [ 'lakshmi-mantra', 'mahalakshmi-mantra', 'shreem-mantra', 'lakshmi-gayatri-mantra' ],
			'related_festivals'=> [ 'varalakshmi-vratham', 'diwali', 'sharad-purnima' ],
			'seo_title_en'     => 'Varalakshmi Vratham Puja Vidhi: Complete Step-by-Step Guide, Samagri & Toram Vidhi',
			'seo_title_te'     => 'వరలక్ష్మీ వ్రత పూజా విధానం: తోరం కట్టే విధానం, కలశ స్థాపన, నైవేద్యాలు, మంత్రాలు',
			'seo_title_hi'     => 'वरलक्ष्मी व्रत पूजा विधि: कलश स्थापना, तोरम बंधन, सामग्री एवं अष्टलक्ष्मी मंत्र',
			'seo_desc_en'      => 'Authentic Varalakshmi Vratham step-by-step puja procedure, Kalasha decoration, 9-knot toram ritual, 9 naivedyam list, and Lakshmi Suktam at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో వరలక్ష్మీ వ్రత కలశ అలంకరణ, తొమ్మిది ముడుల తోరం మంత్రాలు, అష్టలక్ష్మి పూజ మరియు వాయనం సమర్పించే శాస్త్రీయ పద్ధతి.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर वरलक्ष्मी व्रत की प्रामाणिक पूजा विधि, कलश सजावट, तोरम पूजा, नैवेद्य और लक्ष्मी मंत्रों की विस्तृत जानकारी।'
		],

		// ═══════════════════════════════════════════════════════════
		// 4. SHIVA LINGA ABHISHEKAM (Rudrabhishekam)
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'shiva-abhishekam',
			'title'           => 'Shiva Linga Abhishekam (శివ లింగాభిషేకం)',
			'title_en'        => 'Shiva Linga Abhishekam & Rudrabhisheka Vidhanam',
			'title_te'        => 'శివ లింగాభిషేక మరియు రుద్రాభిషేక విధానం',
			'title_hi'        => 'शिव लिंग अभिषेक एवं रुद्राभिषेक पूजा विधि',
			'deity'           => 'Shiva',
			'deity_slug'      => 'shiva',
			'category'        => 'Deity Poojas',
			'duration'        => '1 Hour',
			'intro_en'        => 'Lord Shiva is profoundly pleased by Abhishekam (ritual bathing with sacred substances). Performing Panchamrita Abhishekam on the Shiva Lingam cools the cosmic fire, purifies karmic afflictions, eliminates diseases, and bestows eternal peace (Moksha). Highly auspicious on Mondays, Pradosham, Masa Shivaratri, and Maha Shivaratri.',
			'intro_te'        => '"అభిషేక ప్రియః శివః, అలంకార ప్రియః విష్ణుః" అని ఆర్యోక్తి. పరమశివుడికి పవిత్ర జలాలతో, పంచామృతాలతో అభిషేకం చేయడం వల్ల సకల దోషాలు నివారణమై, ఆయురారోగ్యాలు, మనశ్శాంతి లభిస్తాయి. సోమవారాలు, ప్రదోష కాలం మరియు శివరాత్రి పర్వదినాల్లో రుద్రాభిషేకం విశేష ఫలప్రదం.',
			'intro_hi'        => 'भगवान शिव आशुतोष हैं और केवल जल तथा बेलपत्र के अभिषेक से ही अत्यंत प्रसन्न हो जाते हैं। शिवलिंग पर पंचामृत और गंगाजल की धारा अर्पित करते हुए महामृत्युंजय मंत्र अथवा श्री रुद्रम का पाठ करने से असाध्य रोगों और कालसर्प दोषों से मुक्ति मिलती है।',
			'samagri'         => "• Shiva Lingam with Yoni base and drainage vessel (Abhisheka Patra)\n• Pure cow milk (unboiled), Curd, Pure cow ghee, Honey, Sugarcane juice, Gangajal\n• Fresh three-leaf Bilva patras (బిల్వ పత్రాలు / बेलपत्र)\n• Pure Vibhuti / Bhasma, Sandalwood paste, Akshatas\n• Dhatura flowers, White flowers, Blue lotus\n• Fruits, Dry fruits, Tender coconut water\n• Ghee lamp, Camphor, Dhoop sticks",
			'preparation'     => 'Place the Shiva Lingam on a brass or silver plate with the water spout (Yoni Gomukhi) pointing towards the North. Sit facing East or North. Apply Vibhuti Tripundra and Rudraksha.',
			'sankalpam'       => 'मम कायिक वाचिक मानसिक संसर्गज सकल पापक्षयार्थం, आयुः आरोग्य ऐश्वर्य अभिवृद्ध्यर्थం, श्री साम्ब सदाशिव प्रीतार्थं रुद्राभिषेकं पूजां च करिष्ये ॥',
			'kalasha_sthapana' => 'Fill an abhisheka Kalash with Gangajal and fragrant herbs, invoking the sacred rivers: Ganga, Yamuna, Godavari, Saraswati, Narmada, Sindhu, and Kaveri.',
			'avahanam'        => 'ॐ नमः शिवाय । कैलासशिखरासीनं चन्द्रचूडं महेश्वरम् । आवाहयामि देवेशं त्रिनेत्रं वृषभध्वजम् ॥ Fold hands and invoke Lord Shiva with mother Parvati.',
			'dhyana'          => 'ध्यायेन्नित्यं महेशं रजतगिरिनिभं चारुचन्द्रावतंसं रत्नाकल्पोज्ज्वलाङ्गं परशुमृगवराभीतिहस्तं प्रसन्नम् । पद्मासीनं समन्तात् स्तुतममरगणैर्व्याघ्रकृत्तिं वसानं विश्वाद्यं विश्वबीजं निखिलभयहरं पञ्चवक्त्रं त्रिनेत्रम् ॥',
			'main_puja'       => 'Pour continuous streams over the Lingam: first pure water, then cow milk, curd, honey, ghee, sugarcane juice, coconut water, and finally Gangajal while chanting Sri Rudra Prashna or Shiva Panchakshara Stotram. Dry with clean cloth, apply Chandan and Vibhuti Tripundra, and offer Bilva leaves.',
			'mantra_japa'     => 'Chant Om Namah Shivaya (ॐ नमः शिवाय) or the Maha Mrityunjaya Mantra (ॐ त्र्यम्बकं यजामहे...) 108 times.',
			'naivedyam'       => 'Offer Panchamritam, Ksheerannam (milk kheer), bananas, dry fruits, and tender coconut water.',
			'aarti'           => 'Wave camphor flame singing the Karpura Gauram Karunavataram hymn and traditional Shiva Mangala Aarti.',
			'prarthana'       => 'करचरणकृतं वाक्कायजं कर्मजं वा श्रवणनयनजं वा मानसं वापराधम् । विहितमविहितं वा सर्वमेतत्क्षमस्व जय जय करुणाब्धे श्रीमहादेव शम्भो ॥',
			'prasadam'        => 'Partake of the holy Abhisheka Theertham by taking three sips on the right palm and touch to the eyes. Apply sacred Vibhuti to forehead.',
			'visarjan'        => 'For permanent household Lingams, udvasana is not performed. Offer humble pranams thanking Mahadeva for sanctifying the residence.',
			'vrat_rules'      => 'Observe fasting on Pradosham or Maha Shivaratri until the evening Nishita Kala abhishekam is completed.',
			'faq'             => [
				[
					'q' => 'Which direction should the Shiva Lingam face at home?',
					'a' => 'The devotee should sit facing East, and the spout of the Shiva Linga (Jaladhari/Yoni) must always point towards the North.'
				]
			],
			'related_mantras'  => [ 'om-namah-shivaya', 'maha-mrityunjaya-mantra', 'shiva-panchakshara-stotram', 'shiva-gayatri-mantra' ],
			'related_festivals'=> [ 'maha-shivaratri', 'karthika-masam', 'pradosham' ],
			'seo_title_en'     => 'Shiva Linga Abhishekam & Rudrabhishekam Vidhi: Step-by-Step at Home',
			'seo_title_te'     => 'శివ లింగాభిషేక విధానం: పంచామృత అభిషేకం, బిల్వపత్ర పూజ, రుద్ర మంత్రాలు',
			'seo_title_hi'     => 'शिवलिंग अभिषेक एवं रुद्राभिषेक विधि: पंचामृत, बेलपत्र पूजन एवं मंत्र',
			'seo_desc_en'      => 'Complete Shiva Linga Abhishekam procedure at home with Panchamrita sequence, Bilva patra rules, Maha Mrityunjaya japa, and Aarti at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో ఇంట్లోనే శివలింగానికి పంచామృత అభిషేకం, బిల్వార్చన, రుద్ర మంత్రాలు మరియు తీర్థ స్వీకరణ నియమాలు తెలుసుకోండి.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर घर में शिवलिंग के पंचामृत अभिषेक की संपूर्ण शास्त्रीय विधि, बेलपत्र अर्पण नियम और महामृत्युंजय मंत्र पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 5. SRI RAMA NAVAMI POOJA (Sita Rama Kalyanam)
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'sri-rama-navami-pooja',
			'title'           => 'Sri Rama Navami Pooja & Sita Rama Kalyanam (శ్రీ సీతారామ కల్యాణం)',
			'title_en'        => 'Sri Rama Navami Pooja & Sita Rama Kalyana Vidhi',
			'title_te'        => 'శ్రీ సీతారామ కల్యాణం & శ్రీరామనవమి పూజా విధానం',
			'title_hi'        => 'श्री राम नवमी पूजा एवं सीता-राम कल्याण विधान',
			'deity'           => 'Rama',
			'deity_slug'      => 'rama',
			'category'        => 'Festival Poojas',
			'duration'        => '1.5 Hours',
			'intro_en'        => 'Sri Rama Navami marks the avatar incarnation of Maryada Purushottama Lord Sri Rama at noon (Madhyahna) in Chaitra month. In Telugu regions, this day is celebrated with the divine celestial wedding (Sita Rama Kalyanam), symbolizing the harmony of Dharma and cosmic grace.',
			'intro_te'        => 'చైత్ర శుద్ధ నవమి నాడు మధ్యాహ్నం 12 గంటలకు కర్కాటక లగ్నంలో శ్రీరామచంద్రుడు జన్మించిన పవిత్ర దినం. ఆంధ్రప్రదేశ్ మరియు తెలంగాణలలో భద్రాచలం తరహాలో ప్రతి ఇంటా, ఆలయాల్లో శ్రీ సీతారాముల దివ్య కళ్యాణాన్ని జరిపించి, పానకం, వడపప్పు సమర్పిస్తారు.',
			'intro_hi'        => 'चैत्र मास के शुक्ल पक्ष की नवमी को मर्यादा पुरुषोत्तम भगवान श्रीराम का प्राकट्य हुआ। इस पावन अवसर पर मध्याह्न काल में श्रीराम जन्मोत्सव, सीता-राम विवाह उत्सव, और विशेष पनाकम तथा वड़पप्पू का प्रसाद अर्पित किया जाता है।',
			'samagri'         => "• Idols or portraits of Sri Sita, Rama, Lakshmana, Bharata, Shatrughna, and Anjaneya\n• Yellow cloth, Mangalasutram, Talambralu (rice mixed with turmeric and pearls)\n• Fresh fragrant flowers, Tulasi garlands, Lotus\n• Panakam (Jaggery water with black pepper and cardamom)\n• Vadapappu (Soaked yellow moong dal with chopped coconut and green chilies)\n• Chalimidi (Rice flour mixed with jaggery and cardamom)\n• Bell, Camphor, Incense sticks",
			'preparation'     => 'Decorate the puja mandapam with green mango leaves and flowers. Cleanse the idols with water and panchamritam. Prepare Panakam and Vadapappu in pure brass vessels.',
			'sankalpam'       => 'अद्य श्री रामनवमी शुभतिथौ... मम सकल संशयनिवृत्त्यर्थं, धर्मार्थकाममोक्ष चतुर्विध फलपुरुषार्थ सिद्ध्यर्थం, श्री सीतारामचन्द्र स्वामी प्रीतार्थं श्रीरामपूजनं कल्याणार्चनं च करिष्ये ॥',
			'kalasha_sthapana' => 'Consecrate the Mangala Kalash representing the holy waters of Sarayu river and recite Varuna mantras.',
			'avahanam'        => 'ॐ श्री रामाय नमः । रामाय रामभद्राय रामचन्द्राय वेधसे । रघुनाथाय नाथाय सीतायाः पतये नमः ॥ Invoke Sri Rama accompanied by Janaki Mata.',
			'dhyana'          => 'ध्यायेदाजानुबाहुं धृतशरधनुषं बद्धपद्मासनस्थं पीतं वासो वसानं नवकमलदलस्पर्धिनेत्रं प्रसन्नम् । वामाङ्कारूढसीतामुखकमलमिलल्लोचनं नीरदाभं नानालङ्कारदीप्तं दधतमुरुजटामण्डलं रामचन्द्रम् ॥',
			'main_puja'       => 'Perform Shodashopachara offerings. Conduct Kalyana Mahotsavam with Talambralu recitation: "సీతారాముల కళ్యాణం చూతము రారండి". Recite Sri Rama Ashtottara Shatanama and Nama Ramayana.',
			'mantra_japa'     => 'Chant the Taraka Mantra: श्री राम जय राम जय जय राम (Sri Rama Jaya Rama Jaya Jaya Rama) or ॐ रां रामाय नमः 108 times.',
			'naivedyam'       => 'Offer chilled traditional Panakam, fresh Vadapappu, sweet Chalimidi, Payasam, and seasonal fruits.',
			'aarti'           => 'Wave camphor flame singing the Ramachandra Kripalu Bhajuman hymn and Mangala Aarti.',
			'prarthana'       => 'आपदामपहर्तारं दातारं सर्वसम्पदाम् । लोकाभिरामं श्रीरामं भूयो भूयो नमाम्यहम् ॥',
			'prasadam'        => 'Distribute the refreshing Panakam and Vadapappu to family members and all devotees present.',
			'visarjan'        => 'Thank the Lord for gracing the house with Dharma and bliss, performing respectful udvasana.',
			'vrat_rules'      => 'Observe fasting or consume only satvik fruits until noon (12:00 PM) when the birth of Sri Rama is commemorated.',
			'faq'             => [
				[
					'q' => 'Why are Panakam and Vadapappu offered on Sri Rama Navami?',
					'a' => 'Sri Rama Navami marks the beginning of peak summer (Vasant/Greeshma). Panakam (jaggery, pepper, cardamom) and Vadapappu (soaked moong dal) provide vital electrolytes, cooling benefits, and digestive vitality according to Ayurveda.'
				]
			],
			'related_mantras'  => [ 'sri-rama-taraka-mantra', 'nama-ramayana', 'hanuman-chalisa' ],
			'related_festivals'=> [ 'sri-rama-navami', 'hanuman-jayanti', 'vijayadasami' ],
			'seo_title_en'     => 'Sri Rama Navami Pooja Vidhi: Sita Rama Kalyanam Procedure & Panakam Recipe',
			'seo_title_te'     => 'శ్రీ సీతారామ కల్యాణం & శ్రీరామనవమి పూజా విధానం: పానకం, వడపప్పు తయారీ',
			'seo_title_hi'     => 'श्री राम नवमी पूजा विधि: सीता-राम विवाह, पनाकम नैवेद्य एवं तारक मंत्र',
			'seo_desc_en'      => 'Step-by-step Sri Rama Navami Puja Vidhanam at home with Sita Rama Kalyanam talambralu, Panakam recipe, Vadapappu naivedyam, and Taraka mantra at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో శ్రీరామనవమి మధ్యాహ్న పూజ, సీతారాముల కల్యాణోత్సవం, పానకం వడపప్పు నైవేద్యం మరియు తారక మంత్ర వివరణ.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर श्री राम नवमी की संपूर्ण पूजन विधि, सीता-राम कल्याण उत्सव, पारंपरिक पनाकम और तारक मंत्र पाठ की जानकारी।'
		],

		// ═══════════════════════════════════════════════════════════
		// 6. KRISHNA JANMASHTAMI POOJA
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'krishna-janmashtami-pooja',
			'title'           => 'Sri Krishna Janmashtami Pooja (శ్రీకృష్ణ జన్మాష్టమి పూజ)',
			'title_en'        => 'Sri Krishna Janmashtami Pooja Vidhanam',
			'title_te'        => 'శ్రీ కృష్ణ జన్మాష్టమి పూజా విధానం',
			'title_hi'        => 'श्री कृष्ण जन्माष्टमी पूजा एवं जन्मोत्सव विधि',
			'deity'           => 'Krishna',
			'deity_slug'      => 'krishna',
			'category'        => 'Festival Poojas',
			'duration'        => '1.5 Hours',
			'intro_en'        => 'Sri Krishna Janmashtami celebrates the divine midnight birth of Lord Sri Krishna in Rohini Nakshatra and Ashtami tithi of Shravana/Bhadrapada month. Drawing little infant footprints leading into the house invites Bala Krishna to shower joy, protection, and boundless grace.',
			'intro_te'        => 'శ్రావణ బహుళ అష్టమి అర్ధరాత్రి రోహిణీ నక్షత్రంలో జగద్గురువు శ్రీకృష్ణ పరమాత్మ జన్మించారు. ఇళ్ల ముందు పసిపిల్లల ముద్దుల పాదాల గుర్తులు వేసి, ఉయ్యాలలో బాలకృష్ణుడిని పవళింపజేసి, వెన్న, అటుకులు, పిండివంటలు సమర్పిస్తారు.',
			'intro_hi'        => 'भाद्रपद मास के कृष्ण पक्ष की अष्टमी तिथि की आधी रात को रोहिणी नक्षत्र में भगवान श्रीकृष्ण का अवतरण हुआ। घर में बाल गोपाल के बाल-चरण बनाकर, माखन-मिश्री का भोग लगाकर, कान्हा का जन्मोत्सव मनाया जाता है।',
			'samagri'         => "• Bala Krishna (Laddoo Gopal) idol and cradle (uyyala/jhula)\n• Fresh white butter (venna / makkhan) and sugar candy (mishri)\n• Poha (atukulu / chivda), Cow milk, Honey, Ghee, Panchamritam\n• Peacock feather (nemali pincha), Flute, Yellow Pitambaram\n• Fragrant Parijata and Tulasi leaves\n• Clay diyas, Camphor, Agarbatti, Incense",
			'preparation'     => 'Draw tiny infant Krishna footprints from the threshold to the altar with rice flour paste. Prepare a decorated swing (jhula). Fast through the day until midnight.',
			'sankalpam'       => 'अद्य श्रीकृष्णजन्माष्टमी शुभतिथौ... ममात्मनः सकलपापक्षयार्थं, धर्मार्थकाममोक्ष सिद्ध्यर्थं, श्री सन्तानगोपाल कृष्णप्रीतार्थं श्री कृष्णजन्मोत्सव पूजनं करिष्ये ॥',
			'kalasha_sthapana' => 'Consecrate the holy Kalash invoking Yamuna and sacred Vrindavan rivers.',
			'avahanam'        => 'ॐ क्लीं कृष्णाय गोविंदाय गोपीजनवल्लभाय नमः । देवकीनन्दनं वन्दे वासुदेवं जगद्गुरुम् । आवाहयामि गोपीशं यशोदानन्दवर्धनम् ॥',
			'dhyana'          => 'कस्तूरीतिलकं ललाटपटले वक्षःस्थले कौस्तुभं नासाग्रे वरमौक्तिकं करतले वेणुं करे कङ्कणम् । सर्वाङ्गे हरिचन्दनं सुललितं कण्ठे च मुक्तावलिं गोपस्त्रीपरिवेष्टितो विजयते गोपालचूडामणिः ॥',
			'main_puja'       => 'Bathe Laddoo Gopal with milk, curd, honey, and rose water (Panchamrita Abhishekam). Dress in new yellow Pitambara with peacock feather. Place in swing and gently rock while reciting Krishna Ashtottara Shatanama.',
			'mantra_japa'     => 'Chant the Hare Krishna Mahamantra (हरे कृष्ण हरे कृष्ण कृष्ण कृष्ण हरे हरे | हरे राम हरे राम राम राम हरे हरे) 108 times.',
			'naivedyam'       => 'Offer fresh white butter mixed with mishri, sweet flattened rice (Atukulu), Chakli, Murukku, Laddus, and Panchamritam with fresh Tulasi leaves.',
			'aarti'           => 'Wave camphor flame at midnight (Nishita Kala) singing "Aarti Kunj Bihari Ki" and ringing bells with utter joy.',
			'prarthana'       => 'वसुदेवसुतं देवं कंसचाणूरमर्दनम् । देवकीपरमानन्दं कृष्णं वन्दे जगद्गुरुम् ॥',
			'prasadam'        => 'Break the fast with blessed Makhan Mishri and Panchamritam distributed among all participants.',
			'visarjan'        => 'Place Bala Krishna respectfully on the permanent shrine while singing lullabies (lali patalu).',
			'vrat_rules'      => 'Observe complete fast or milk/fruit fasting until midnight (12:00 AM) when the Avatara takes place.',
			'faq'             => [
				[
					'q' => 'Why are tiny baby footprints drawn into the house on Janmashtami?',
					'a' => 'They signify inviting baby Krishna into one\'s home and heart, bringing the joy, laughter, and spiritual protection that blessed Gokulam.'
				]
			],
			'related_mantras'  => [ 'krishna-mantra', 'hare-krishna-mahamantra', 'madhurashtakam', 'achutashtakam' ],
			'related_festivals'=> [ 'krishna-janmashtami', 'govardhan-puja', 'radhashtami' ],
			'seo_title_en'     => 'Krishna Janmashtami Pooja Vidhi: Midnight Janmotsav, Makkhan Naivedyam & Mantras',
			'seo_title_te'     => 'శ్రీకృష్ణ జన్మాష్టమి పూజా విధానం: అర్ధరాత్రి పూజ, వెన్న నైవేద్యం, బాలగోపాల పూజ',
			'seo_title_hi'     => 'श्री कृष्ण जन्माष्टमी पूजा विधि: मध्यरात्रि जन्मोत्सव, माखन-मिश्री भोग एवं मंत्र',
			'seo_desc_en'      => 'Complete Krishna Janmashtami Puja Vidhanam at home with midnight Laddoo Gopal abhishekam, swing ritual, Makhan Mishri naivedyam, and mantras at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో జన్మాష్టమి అర్ధరాత్రి బాలకృష్ణ పూజ, ఉయ్యాల సేవ, వెన్న అటుకుల నైవేద్యం మరియు కృష్ణ మంత్రాల సంపూర్ణ సమాచారం.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर बाल गोपाल के मध्यरात्रि जन्मोत्सव की शास्त्रीय विधि, पंचामृत अभिषेक, माखन भोग और कृष्ण मंत्र पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 7. SRI HANUMAN PUJA VIDHI
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'hanuman-puja-vidhi',
			'title'           => 'Sri Hanuman Puja & Sindoor Archana Vidhi (శ్రీ హనుమాన్ పూజ)',
			'title_en'        => 'Sri Hanuman Puja & Sindoor Archana Vidhanam',
			'title_te'        => 'శ్రీ హనుమాన్ పూజా విధానం & సింధూరార్చన',
			'title_hi'        => 'श्री हनुमान पूजा एवं सिन्दूर अर्चन विधि',
			'deity'           => 'Hanuman',
			'deity_slug'      => 'hanuman',
			'category'        => 'Deity Poojas',
			'duration'        => '45 Minutes',
			'intro_en'        => 'Lord Hanuman is the epitome of courage, humility, unwavering devotion to Sri Rama, and mastery over senses. Worshiping Anjaneya with Sindoor, Betel leaf garlands (Aku Pooja), and chanting the Chalisa dispels fear, Saturn (Shani) afflictions, nightmares, and negative energies.',
			'intro_te'        => 'భక్తాగ్రేసరుడు, చిరంజీవి అయిన శ్రీ ఆంజనేయ స్వామి పూజ సకల భయాలను, శని దోషాలను, దుష్ట గ్రహ పీడలను పటాపంచలు చేస్తుంది. తమలపాకుల మాల (ఆకు పూజ), సింధూరార్చన, వడమాల సమర్పణ మరియు హనుమాన్ చాలీసా పఠనం అత్యంత ప్రీతికరం.',
			'intro_hi'        => 'संकटमोचन श्री हनुमान जी की साधना जीवन के समस्त भय, संकट, रोग और शनि दोषों का निवारण करने वाली है। मंगलवार और शनिवार को सिन्दूर अर्चन, चमेली का तेल, पान का बीड़ा और हनुमान चालीसा पाठ का विशेष फल है।',
			'samagri'         => "• Hanuman idol, portrait, or Yantra\n• Pure orange Sindoor and Jasmine oil (Chameli tel)\n• Betel leaf garland (ఆకుల పూజ / 108 पान के पत्तों की माला)\n• Garland of Vada (గారెల మాల / उड़द दाल के वड़े)\n• Bananas, Pomegranate, Jaggery and Roasted gram (Gud Chana)\n• Red flowers and Red cloth (Langot)\n• Ghee or Mustard oil lamp, Camphor, Dhoop",
			'preparation'     => 'Take an early morning bath, wear red or saffron clothing. Sit facing East or North on a woolen mat. Light a deepam with sesame or mustard oil.',
			'sankalpam'       => 'मम सकलभयनिवारणार्थं, ग्रहापीडानिवारणार्थं, आयुर्बलारोग्य सिद्ध्यर्थं श्री रामभक्त हनुमान प्रीतार्थं ध्यानपूजां करिष्ये ॥',
			'kalasha_sthapana' => 'Place a holy Kalash consecrated with water and akshatas in front of the shrine.',
			'avahanam'        => 'ॐ हं हनुमते नमः । मनोजवं मारुततुल्यवेगं जितेन्द्रियं बुद्धिमतां वरिष्ठम् । वातात्मजं वानरयूथमुख्यं श्रीरामदूतं शरणं प्रपद्ये ॥',
			'dhyana'          => 'अतुलितबलधामं हेमशैलाभदेहं दनुजवनकृशानुं ज्ञानिनाPrefix्रगण्यम् । सकलगुणनिधानं वानराणामधीशं रघुपतिप्रियभक्तं वातजातं नमामि ॥',
			'main_puja'       => 'Apply sacred orange Sindoor mixed with Chameli oil to Lord Hanuman. Offer the 108 betel leaf garland. Recite Hanuman Ashtottara Shatanama offering red flowers and akshatas.',
			'mantra_japa'     => 'Chant the Hanuman Moola Mantra: ॐ हं हनुमते नमः (Om Hum Hanumate Namah) or the Hanuman Gayatri 108 times, followed by chanting the Hanuman Chalisa.',
			'naivedyam'       => 'Offer Vada garland, sweet boondi laddus, jaggery mixed with roasted chickpeas (Gud Chana), sweet betel pan, and bananas.',
			'aarti'           => 'Light pure camphor and sing "Aarti Kije Hanuman Lala Ki" while waving the flame reverently.',
			'prarthana'       => 'बुद्धिर्बलं यशो धैर्यं निर्भयत्वमरोगता । अजाड्यं वाक्पटुत्वं च हनुमत्स्मरणाद्भवेत् ॥',
			'prasadam'        => 'Distribute the laddus, roasted gram, and vadas to all family members and visitors.',
			'visarjan'        => 'Bow with folded hands, asking Anjaneya to remain always as the guardian of the home.',
			'vrat_rules'      => 'Maintain celibacy and truthfulness on Tuesdays and Saturdays. Avoid non-vegetarian food, alcohol, and negative speech.',
			'faq'             => [
				[
					'q' => 'Why is Sindoor offered to Lord Hanuman?',
					'a' => 'When Sita Mata explained that she wore Sindoor in her parting for the long life of Sri Rama, Hanuman lovingly smeared his entire body with Sindoor so Sri Rama would live forever. The Lord was overjoyed by this selfless love.'
				]
			],
			'related_mantras'  => [ 'hanuman-mantra', 'hanuman-chalisa', 'hanuman-gayatri-mantra', 'sankat-mochan-hanuman-ashtak' ],
			'related_festivals'=> [ 'hanuman-jayanti', 'sri-rama-navami' ],
			'seo_title_en'     => 'Sri Hanuman Puja Vidhi: Sindoor Archana, Aku Pooja & Chalisa Japa',
			'seo_title_te'     => 'శ్రీ హనుమాన్ పూజా విధానం: సింధూరార్చన, ఆకుల పూజ, వడమాల, చాలీసా పఠనం',
			'seo_title_hi'     => 'श्री हनुमान पूजा विधि: सिन्दूर अर्चन, पान माला, वड़ा भोग एवं चालीसा पाठ',
			'seo_desc_en'      => 'Complete Tuesday and Saturday Hanuman Puja Vidhanam with Sindoor offering, betel leaf garland, Hanuman Chalisa japa, and Aarti at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో ఆంజనేయ స్వామి సింధూరార్చన, తమలపాకుల మాల పూజ, శని దోష నివారణ మరియు హనుమాన్ చాలీసా విశేషాలు.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर श्री हनुमान पूजा की प्रामाणिक विधि, सिन्दूर अर्पण, चमेली का तेल, संकटमोचन अष्टक और आरती पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 8. DIWALI LAKSHMI & KUBERA PUJA
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'lakshmi-puja-vidhi',
			'title'           => 'Diwali Lakshmi & Kubera Puja (దీపావళి లక్ష్మీ కుబేర పూజ)',
			'title_en'        => 'Diwali Lakshmi & Kubera Puja Vidhanam',
			'title_te'        => 'దీపావళి శ్రీ మహాలక్ష్మి & కుబేర పూజా విధానం',
			'title_hi'        => 'दीपावली लक्ष्मी एवं कुबेर पूजा विधान',
			'deity'           => 'Lakshmi',
			'deity_slug'      => 'lakshmi',
			'category'        => 'Festival Poojas',
			'duration'        => '1.5 Hours',
			'intro_en'        => 'On the auspicious night of Deepavali (Ashwina Amavasya during Pradosha Kala), Goddess Lakshmi visits pure and illuminated homes to bestow wealth, good fortune, and spiritual splendour. Along with Mahalakshmi, Lord Kubera (treasurer of heavens) and Lord Ganesha are worshiped.',
			'intro_te'        => 'ఆశ్వయుజ బహుళ అమావాస్య ప్రదోష కాలంలో దీపాలతో వెలిగిపోయే ఇళ్లలోకి లక్ష్మీదేవి ప్రవేశిస్తుందని నమ్ముతారు. చీకట్లను పారద్రోలి జ్ఞానాన్ని, సిరిసంపదలను ప్రసాదించే శ్రీ మహాలక్ష్మి, విఘ్నేశ్వరుడు మరియు ధనాధిపతి కుబేరుడి పూజ దీపావళి నాడు సంపూర్ణంగా నిర్వహించబడుతుంది.',
			'intro_hi'        => 'दीपावली की पावन रात्रि (प्रदोष काल) में महालक्ष्मी, गणेश जी और धन के अधिपति कुबेर जी का विशेष पूजन किया जाता है। घरों में मिट्टी के दीप जलाकर दरिद्रता का नाश और अखंड सौभाग्य का स्वागत किया जाता है।',
			'samagri'         => "• Silver or Gold coins (Lakshmi-Ganesh coin)\n• Clay Diyas (మట్టి ప్రమిదలు / मिट्टी के दीपक) with cow ghee or sesame oil\n• Red and yellow flowers, Fresh Lotus flowers (తామర పూలు / कमल पुष्प)\n• Kalash, Mango leaves, Coconut, Unbroken raw rice (Akshatas)\n• Account books / Ledger (Bahi Khata) for commercial prosperity\n• Sweets (Laddus, Kheer, Kaju Katli), Poha, Puffed rice (Murmura/Pelalu), Batasha",
			'preparation'     => 'Decorate doorways with mango leaves and marigold garlands. Cleanse money chest/locker. Set up high peetham with red cloth, place idols of Ganesha on the left and Lakshmi on the right.',
			'sankalpam'       => 'अद्य दीपावली पुण्यतिथौ प्रदोषकाले... मम गृहे स्थिरलक्ष्मी, धनधान्य, समृद्ध्यादि सिद्ध्यर्थं श्री महालक्ष्मी महाकाली महासरस्वती कुबेर प्रीतार्थं पूजनं करिष्ये ॥',
			'kalasha_sthapana' => 'Consecrate the Mangala Kalash with coins, holy water, mango leaves, and coconut decorated with swastika.',
			'avahanam'        => 'ॐ श्रीं ह्रीं क्लीं त्रिभुवनमहालक्ष्म्यै अस्माकं दारिद्र्यं नाशय नाशय प्रसीद प्रसीद श्रीं ह्रीं क्लीं ॐ ॥ Invoking Mahalakshmi and Kubera.',
			'dhyana'          => 'वन्दे पद्मकरां प्रसन्नवदनां सौभाग्यदां भाग्यदां हस्ताभ्यामभयप्रदां मणिरणन्नूपुरैर्भूषिताम् । श्वेताभ्राम्बरधारिणीं शशिनिभां लक्ष्मीं हरिप्रियां ध्यायेच्चेतसि सर्वकामफलदां संसारबीजाङ्कुराम् ॥',
			'main_puja'       => 'First worship Ganesha for removing obstacles, then perform Shodashopachara to Mahalakshmi offering Lotus, Kumkum archana, and Kubera Yantra archana reciting 108 names.',
			'mantra_japa'     => 'Chant Om Shreem Mahalakshmyai Namah (ॐ श्रीं महालक्ष्म्यै नमः) and the Kubera Mantra: ॐ यक्षाय कुबेराय वैश्रवणाय धनधान्याधिपतये धनधान्यसमृद्धिं मे देहि दापय स्वाहा ॥',
			'naivedyam'       => 'Offer Kheer (Payasam), fresh laddus, puffed rice (pelalu/khil), batasha, dry fruits, and seasonal fruits.',
			'aarti'           => 'Light a grand 5-wick Aarti and pure camphor, singing "Om Jai Lakshmi Mata" and traditional Telugu Deepavali songs.',
			'prarthana'       => 'सर्वमङ्गलमाङ्गल्ये शिवे सर्वार्थसाधिके । शरण्ये त्र्यम्बకే गौरि नारायणि नमोऽस्तु ते ॥',
			'prasadam'        => 'Distribute sweets to family and neighbours, and light diyas across all rooms, balconies, and entryways.',
			'visarjan'        => 'The Lakshmi Kalash remains overnight, and gentle visarjan is done on the following morning after punah-puja.',
			'vrat_rules'      => 'Maintain joyous atmosphere, avoid quarrels, avoid gambling, and keep the home brightly lit throughout the night.',
			'faq'             => [
				[
					'q' => 'Why is Lord Ganesha always placed on the right hand side of Goddess Lakshmi?',
					'a' => 'As the universal mother, Lakshmi adopted Ganesha as her divine son. In Sanatana tradition, the mother sits with her beloved child beside her to ensure that wealth is guided by wisdom and righteousness.'
				]
			],
			'related_mantras'  => [ 'lakshmi-mantra', 'mahalakshmi-mantra', 'kuber-mantra', 'shreem-mantra' ],
			'related_festivals'=> [ 'diwali', 'dhanteras', 'naraka-chaturdashi' ],
			'seo_title_en'     => 'Diwali Lakshmi & Kubera Puja Vidhi: Muhurat, Samagri & Step-by-Step Procedure',
			'seo_title_te'     => 'దీపావళి లక్ష్మీ కుబేర పూజా విధానం: ప్రదోష ముహూర్తం, పూజా సామగ్రి, మంత్రాలు',
			'seo_title_hi'     => 'दीपावली लक्ष्मी एवं कुबेर पूजा विधि: प्रदोष मुहूर्त, सामग्री एवं संपूर्ण मंत्र',
			'seo_desc_en'      => 'Step-by-step Diwali Lakshmi and Kubera Puja Vidhanam at home with Pradosha muhurat, Lotus offering, Bahi-Khata pooja, and Kubera mantras at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో దీపావళి లక్ష్మీ పూజా విధానం, కుబేర మంత్రం, కమల పుష్పార్చన మరియు దీపారాధన నియమాలు తెలుసుకోండి.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर दीपावली प्रदोष काल में महालक्ष्मी एवं कुबेर पूजन की शास्त्रीय विधि, श्री सूक्त पाठ और आरती पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 9. VASANT PANCHAMI SARASWATI PUJA & VIDYARAMBHAM
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'saraswati-puja-vidhi',
			'title'           => 'Vasant Panchami Saraswati Puja & Vidyarambham (సరస్వతీ పూజ & అక్షరాభ్యాసం)',
			'title_en'        => 'Vasant Panchami Saraswati Puja & Vidyarambham Vidhi',
			'title_te'        => 'సరస్వతీ పూజ & అక్షరాభ్యాస విధానం (వసంత పంచమి)',
			'title_hi'        => 'सरस्वती पूजा एवं विद्यारम्भ विधान (वसंत पंचमी)',
			'deity'           => 'Saraswati',
			'deity_slug'      => 'saraswati',
			'category'        => 'Festival Poojas',
			'duration'        => '1 Hour',
			'intro_en'        => 'Vasant Panchami marks the advent of spring and the appearance day of Goddess Saraswati, embodiment of speech, wisdom, music, and arts. On this auspicious day, books, musical instruments, and pens are sanctified, and children are introduced to formal education through Aksharabhyasam (Vidyarambham).',
			'intro_te'        => 'మాఘ శుద్ధ పంచమి నాడు జ్ఞాన ప్రదాయిని, వాగ్దేవి అయిన శ్రీ సరస్వతీ దేవిని ఆరాధిస్తారు. ఈ దినాన చిన్నారులకు అక్షరాభ్యాసం (ఓం నమః శివాయ / శ్రీరామ అక్షర లేఖనం) చేయించడం వల్ల సకల విద్యల్లో ఉత్తీర్ణులవుతారు. విద్యార్థులు పుస్తకాలు, పెన్నులు పీఠంపై ఉంచి పూజిస్తారు.',
			'intro_hi'        => 'माघ मास के शुक्ल पक्ष की पंचमी को मां सरस्वती का प्राकट्य हुआ। इस दिन पीले वस्त्र पहनकर वाणी, बुद्धि और कला की अधिष्ठात्री देवी का पूजन किया जाता है। नन्हे बच्चों का विद्यारम्भ (अक्षराभ्यास) कराने के लिए यह सर्वोत्तम दिन है।',
			'samagri'         => "• Idol or portrait of Goddess Saraswati\n• White and yellow flowers (Jasmine, Marigold, Yellow Chrysanthemum)\n• Slate and slate pencil, or plate of raw rice for Aksharabhyasam\n• Books, notebooks, musical instruments, and pen\n• White sandalwood paste, Turmeric, Kumkum\n• Boondi, Sweet yellow rice (Kesari Baath), Bananas, Mishri\n• Cow ghee lamp and white wicks",
			'preparation'     => 'Dress in yellow or white traditional clothing. Cleanse the study altar. Place books and pens neatly in front of Goddess Saraswati portrait. Keep a plate of raw rice ready for writing letters.',
			'sankalpam'       => 'अद्य वसन्तपञ्चमी शुभतिथौ... मम बुद्धिविकासार्थं, विद्यापारङ्गत्वार्थं, वाक्सिद्ध्यर्थं श्री महासरस्वती प्रीतार्थं पूजनं विद्यारम्भं च करिष्ये ॥',
			'kalasha_sthapana' => 'Consecrate a Kalash with pure water representing the celestial Saraswati river.',
			'avahanam'        => 'ॐ ऐं सरस्वत्यै नमः । सरस्वति नमस्तुभ्यं वरदे कामरूपिणि । विद्यारम्भं करिष्यामि सिद्धिर्भवतु मे सदा ॥',
			'dhyana'          => 'या कुन्देन्दुतुषारहारधवला या शुभ्रवस्त्रावृता या वीणावरदण्डमण्डितकरा या श्वेतपद्मासना । या ब्रह्माच्युतशङ्करप्रभृतिभिर्देवैः सदा वन्दिता सा मां पातु सरस्वती भगवती निःशेषजाड्यापहा ॥',
			'main_puja'       => 'Perform Shodashopachara worship with white and yellow flowers. Chant Saraswati Ashtottara Shatanama. For children: Guide the child\'s index finger in raw rice to write "ॐ" and "श्रीं".',
			'mantra_japa'     => 'Chant the Saraswati Beeja Mantra: ॐ ऐं सरस्वत्यै नमः (Om Aim Saraswatyai Namah) or Vidya Mantra 108 times.',
			'naivedyam'       => 'Offer sweet yellow saffron rice (Kesari), sweet boondi, bananas, honey, and white kheer.',
			'aarti'           => 'Wave pure camphor singing Saraswati Aarti and offering salutations to teachers and elders.',
			'prarthana'       => 'शुक्लां ब्रह्मविचारसारपरमामाद्यां जगद्व्यापिनीं वीणापुस्तकधारिणीमभयदां जाड्यान्धकारापहाम् । हस्ते स्फाटिकमालिकां विदधतीं पद्मासने संस्थितां वन्दे तां परमेश्वरीं भगवतीं बुद्धिप्रदां शारदाम् ॥',
			'prasadam'        => 'Distribute the sweet yellow rice to students, teachers, and family members.',
			'visarjan'        => 'Touch the feet of Goddess Saraswati and touch eyes with study materials seeking unending wisdom.',
			'vrat_rules'      => 'Observe satvik food habits. Dedicate the day to reading, writing, or practicing musical notes.',
			'faq'             => [
				[
					'q' => 'How is Aksharabhyasam conducted during this puja?',
					'a' => 'The father, maternal uncle, or teacher holds the child’s right index finger and gently traces the sacred syllable "ॐ" (Om) or "श्री" on a plate filled with raw grains of rice.'
				]
			],
			'related_mantras'  => [ 'saraswati-mantra', 'saraswati-gayatri-mantra', 'vidya-mantra' ],
			'related_festivals'=> [ 'vasant-panchami', 'vijayadasami' ],
			'seo_title_en'     => 'Vasant Panchami Saraswati Puja Vidhi & Aksharabhyasam Procedure',
			'seo_title_te'     => 'సరస్వతీ పూజ & అక్షరాభ్యాస విధానం: వసంత పంచమి పూజా నియమాలు, మంత్రాలు',
			'seo_title_hi'     => 'सरस्वती पूजा एवं विद्यारम्भ विधि: वसंत पंचमी पूजन, सामग्री एवं मंत्र',
			'seo_desc_en'      => 'Step-by-step Vasant Panchami Saraswati Puja Vidhanam at home with Aksharabhyasam guidelines, yellow dress significance, and Saraswati Beeja mantra at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో వసంత పంచమి సరస్వతీ పూజా విధానం, అక్షరాభ్యాసం చేయించే పద్ధతి, పసుపు నైవేద్యం మరియు వాగ్దేవి స్తోత్రాలు.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर मां सरस्वती की प्रामाणिक पूजा विधि, बच्चों के विद्यारम्भ के नियम, पीला भोग और सरस्वती द्वादश नाम पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 10. DURGA NAVARATRI PUJA & CHANDI VIDHI
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'durga-navaratri-puja-vidhi',
			'title'           => 'Sharad Navratri Durga Puja Vidhi (శరన్నవరాత్రి దుర్గా పూజ)',
			'title_en'        => 'Sharad Navratri Durga Puja & Chandi Vidhanam',
			'title_te'        => 'శరన్నవరాత్రి దుర్గా పూజా విధానం & చండీ పారాయణ',
			'title_hi'        => 'शारदीय नवरात्रि दुर्गा पूजा एवं चण्डी पाठ विधि',
			'deity'           => 'Durga',
			'deity_slug'      => 'durga',
			'category'        => 'Festival Poojas',
			'duration'        => '2 Hours',
			'intro_en'        => 'Sharad Navratri is the divine nine-night festival celebrating the victory of Supreme Goddess Durga over Mahishasura. Each day invokes one of the Navadurga forms (Shailaputri to Siddhidatri). The puja involves Ghatasthapana (Kalash installation), lighting the Akhanda Deepam, Chandi/Devi Mahatmyam recitation, and Kanya Puja.',
			'intro_te'        => 'ఆశ్వయుజ శుద్ధ పాడ్యమి నుండి నవమి వరకు తొమ్మిది రాత్రులు లోకకళ్యాణార్థం దుర్గాదేవిని నవదుర్గల రూపాల్లో ఆరాధిస్తారు. కలశ స్థాపన (ఘటస్థాపన), అఖండ దీపారాధన, కుంకుమార్చన, దుర్గా సప్తశతి పారాయణ మరియు కన్యాపూజలతో భక్తులు అమ్మవారి అనుగ్రహాన్ని పొందుతారు.',
			'intro_hi'        => 'शारदीय नवरात्रि में शक्तिस्वरूपा मां दुर्गा के नौ पावन रूपों (शैलपुत्री से सिद्धिदात्री) की नौ दिनों तक निरंतर उपासना की जाती है। घटस्थापना, अखंड ज्योति, दुर्गा सप्तशती पाठ और कन्या पूजन से भक्तों के सभी भय और कष्ट दूर होते हैं।',
			'samagri'         => "• Durga idol or photo\n• Clay pot for Ghatasthapana, clean soil, and barley seeds (Jowar / Saptadhanya)\n• Brass/Copper Kalash, Mango leaves, Coconut, Red cloth\n• Red Hibiscus, Red rose garlands, Lotus\n• Pure red Kumkum for Kumkumarchana\n• Cow ghee, Akhanda Jyoti brass lamp, Long wicks\n• Durga Saptashati text, Camphor, Dhoop\n• Sweets, Poha, Fruits, Halwa, Chana, and Puri for Kanya Puja",
			'preparation'     => 'Clean altar facing North-East. Sow sacred barley seeds (Jowar) in a wide earthen pot with moistened earth. Consecrate the Kalash over the pot. Light the Akhanda Deepam to burn throughout the 9 days.',
			'sankalpam'       => 'अद्य अश्विनशुक्लप्रतिपत्तिथौ... मम सर्वोपद्रवनिवारणार्थं, आयुरारोग्यैश्वर्य विजयसिद्ध्यर्थं श्री महाकाली महालक्ष्मी महासरस्वती स्वरूपिणी श्री दुर्गाप्रीतार्थं नवरत्नपूजनं घटस्थापनं च करिष्ये ॥',
			'kalasha_sthapana' => 'Place Kalash upon the sown earth with mango leaves and coconut wrapped in red cloth. Recite Vedic Varuna mantras.',
			'avahanam'        => 'ॐ ऐं ह्रीं क्लीं चामुण्डायै विच्चे । महिषघ्नि महामाये चामुण्डे मुण्डमालिनि । आयूमारोग्यमैश्वर्यं देहि देवि नमोऽस्तु ते ॥ Invoking Jagadamba.',
			'dhyana'          => 'विद्युद्दामसमप्रभां मृगपतिस्कन्धस्थितां भीषणां कन्याभिः करवालखेटविलसद्धस्ताभिरासेविताम् । हस्तैश्चक्रगदासिखेटविशिखांश्चापं गुणं तर्जनीं विभ्राणामनलात्मिकां शशिधरां दुर्गां त्रिनेत्रां भजे ॥',
			'main_puja'       => 'Offer Red Vastra, Red flowers, perform Lalitha Sahasranama or Durga Ashtottara Kumkumarchana. Recite Argala Stotram, Kilakam, and Devi Kavacham.',
			'mantra_japa'     => 'Chant the powerful Navarna Mantra: ॐ ऐं ह्रीं क्लीं चामुण्डायै विच्चे (Om Aim Hreem Kleem Chamundayai Vicche) 108 times.',
			'naivedyam'       => 'Offer sweet halwa, roasted black chana, puffed puri, fruits, and panchamritam daily.',
			'aarti'           => 'Wave camphor and ghee wicks while singing "Ambe Tu Hai Jagdambe Kali" or the traditional Telugu Lalitha Mangala Aarti.',
			'prarthana'       => 'सर्वमङ्गलमाङ्गल्ये शिवे सर्वार्थसाधिके । शरण्ये त्र्यम्बके गौरि नारायणि नమోऽस्तु ते ॥ शरणागतदीनार्तपरित्राणपरायणे । सर्वस्यार्तिहरे देवि नारायणि नमोऽस्तु ते ॥',
			'prasadam'        => 'Perform Kanya Puja on Ashtami or Navami (worshiping nine young girls representing Navadurga), feed them halwa-puri, offer gifts, and take their blessings.',
			'visarjan'        => 'On Vijayadasami morning, perform Punah-puja and respectfully immerse the consecrated sprouted barley grass in clean garden soil or river.',
			'vrat_rules'      => 'Observe fasting on fruits and milk, or single satvik meal. Avoid anger, shaving, cutting nails, and leather items throughout the 9 days.',
			'faq'             => [
				[
					'q' => 'What is the significance of the sprouted barley (Jowar) in Ghatasthapana?',
					'a' => 'The green sprouted barley represents fertility, prosperity, and the life-force of Mother Earth. Healthy green sprouts signify good fortune and divine abundance for the coming year.'
				]
			],
			'related_mantras'  => [ 'durga-mantra', 'durga-gayatri-mantra', 'navarna-mantra', 'devi-mantra' ],
			'related_festivals'=> [ 'navaratri', 'vijayadasami' ],
			'seo_title_en'     => 'Sharad Navratri Durga Puja Vidhi: Ghatasthapana, Akhanda Jyot & Navarna Mantra',
			'seo_title_te'     => 'శరన్నవరాత్రి దుర్గా పూజా విధానం: కలశ స్థాపన, అఖండ దీపం, కుంకుమార్చన, నవార్ణ మంత్రం',
			'seo_title_hi'     => 'शारदीय नवरात्रि दुर्गा पूजा विधि: घटस्थापना, अखंड ज्योति, सप्तशती पाठ एवं कन्या पूजन',
			'seo_desc_en'      => 'Step-by-step Navratri Durga Puja Vidhanam at home with Ghatasthapana, Kumkumarchana, 9 Navadurga days, Navarna mantra, and Kanya puja at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో శరన్నవరాత్రుల ఘటస్థాపన, అఖండ జ్యోతి వెలిగించే విధానం, లలితా సహస్రనామ కుంకుమార్చన మరియు కన్యా పూజా విశేషాలు.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर नवरात्रि घटस्थापना, अखंड दीपक के नियम, नवार्ण मंत्र जप और नवमी कन्या पूजन की संपूर्ण विधि पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 11. SUBRAHMANYA SWAMY POOJA (Skanda Shashti)
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'subrahmanya-swamy-pooja',
			'title'           => 'Sri Subrahmanya Swamy Pooja (సుబ్రహ్మణ్య స్వామి పూజ)',
			'title_en'        => 'Sri Subrahmanya Swamy Puja & Skanda Shashti Vidhi',
			'title_te'        => 'శ్రీ సుబ్రహ్మణ్య స్వామి పూజా విధానం & షష్ఠి వ్రతం',
			'title_hi'        => 'श्री सुब्रह्मण्य (कार्तिकेय) स्वामी पूजा एवं स्कन्द षष्ठी विधान',
			'deity'           => 'Subrahmanya',
			'deity_slug'      => 'subrahmanya',
			'category'        => 'Deity Poojas',
			'duration'        => '1 Hour',
			'intro_en'        => 'Lord Subrahmanya (Kartikeya / Murugan / Skanda), the commander of celestial forces, is the vanquisher of darkness (Surapadma and Taraka asuras). Worshiping Lord Murugan on Shashti or Tuesdays cures Kuja Dosha (Mars afflictions), Sarpa Dosha, skin ailments, and grants victory and marital bliss.',
			'intro_te'        => 'శివపార్వతుల ద్వితీయ కుమారుడు, దేవసేనాధిపతి శ్రీ సుబ్రహ్మణ్య స్వామి. నాగరూపుడైన సుబ్రహ్మణ్యుడిని మంగళవారాలు మరియు స్కంద షష్ఠి నాడు ఆరాధించడం వల్ల కుజ దోషం, సర్ప దోషాలు, కాలసర్ప దోషాలు నివారణమై సంతాన భాగ్యం, విజయం లభిస్తాయి.',
			'intro_hi'        => 'भगवान शिव के ज्येष्ठ पुत्र और देवसेनापति कार्तिकेय (मुरुगन) की पूजा से मंगल दोष, सर्पदोष और शत्रुओं से मुक्ति मिलती है। स्कन्द षष्ठी तथा मंगलवार को षडानन भगवान की उपासना से तेज, पराक्रम और आरोग्य की प्राप्ति होती है।',
			'samagri'         => "• Subrahmanya idol, portrait, or Vel (divine spear)\n• Red flowers, Oleander (Ganneru), Red rose garlands\n• Panchamritam ingredients (Milk, Curd, Ghee, Honey, Sugar)\n• Vibhuti (Bhasma), Sandalwood paste, Kumkum\n• Betel leaves, Supari, Bananas, Pomegranate\n• Sweet Pongal (Chakkara Pongali), Vadapappu, Panakam\n• Cow ghee lamps, Camphor, Dhoop",
			'preparation'     => 'Observe bathing at dawn. Wear red or yellow garments. Place the Vel or Subrahmanya idol on a clean brass plate facing East.',
			'sankalpam'       => 'अद्य कुजदोष सर्पदोष निवारणार्थं, आत्मनः सन्तानप्राप्त्यर्थं, विजयसिद्ध्यर्थं श्री सुब्रह्मण्य स्वामी प्रीतार्थं पूजनं करिष्ये ॥',
			'kalasha_sthapana' => 'Consecrate a Kalash with pure water and mango leaves in front of the Vel.',
			'avahanam'        => 'ॐ षण्मुखाय नमः । ॐ शरवणभवाय नमः । देवसेनापते स्कन्द कार्तिकेय नमोऽस्तु ते ॥ Invoking Lord Murugan.',
			'dhyana'          => 'ध्यायेत् षडाननं देवं मयूरवरवाहनम् । शक्तिं च त्रिशूलं खड्गं बाणं चापं वरमभयम् । दधानं द्वादशभुजं दिव्यतेजोमयं विभुम् ॥',
			'main_puja'       => 'Perform Panchamrita Abhishekam to the Vel / idol. Smear with cooling sandalwood paste and pure Vibhuti. Offer red oleander flowers and chant the Subrahmanya Ashtottara Shatanama.',
			'mantra_japa'     => 'Chant the Shadakshara Mantra: ॐ शरवणभवाय नमः (Om Sharavana Bhavaya Namah) 108 times, followed by Subrahmanya Bhujangam.',
			'naivedyam'       => 'Offer Sweet Chakkara Pongali, Panakam, fresh pomegranate, bananas, and soaked moong dal (Vadapappu).',
			'aarti'           => 'Wave camphor flame singing Skanda hymns and ringing the bell enthusiastically.',
			'prarthana'       => 'शक्तिहस्तं विरूपाक्षं शिखिवाहमघान्तकम् । मेघनादं महासेनं सुब्रह्मण्यं नमाम्यहम् ॥',
			'prasadam'        => 'Distribute the blessed sweet Pongali to family members and children.',
			'visarjan'        => 'Offer respectful sashtanga pranams to the divine Vel, seeking courage and protection.',
			'vrat_rules'      => 'Observe fasting on Shashti tithi, consuming only milk or fruits once a day.',
			'faq'             => [
				[
					'q' => 'Why is Lord Subrahmanya associated with the serpent (Naga) form in Telugu and South Indian traditions?',
					'a' => 'In South Indian theology, Lord Skanda symbolizes the awakening of the sacred Kundalini energy (symbolized as a serpent) and divine yogic vitality, resolving all Sarpa doshas.'
				]
			],
			'related_mantras'  => [ 'subrahmanya-mantra', 'skanda-gayatri-mantra' ],
			'related_festivals'=> [ 'subrahmanya-shashti', 'nag-panchami' ],
			'seo_title_en'     => 'Sri Subrahmanya Swamy Pooja Vidhi: Kuja Dosha Nivarana & Skanda Shashti Procedure',
			'seo_title_te'     => 'శ్రీ సుబ్రహ్మణ్య స్వామి పూజా విధానం: కుజ దోష నివారణ, వేల్ పూజ, షష్ఠి వ్రతం',
			'seo_title_hi'     => 'श्री सुब्रह्मण्य स्वामी पूजा विधि: मंगल दोष निवारण, स्कन्द षष्ठी एवं षण्मुख मंत्र',
			'seo_desc_en'      => 'Complete Subrahmanya Swamy Puja Vidhanam at home with Vel abhishekam, Sarpa dosha nivarana, Sharavana Bhava mantra, and Skanda Shashti vrat rules at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో సుబ్రహ్మణ్య స్వామి అభిషేకం, వేల్ ఆరాధన, షడక్షరీ మంత్రం మరియు సర్ప దోష పరిహార పూజా నియమాలు.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर भगवान कार्तिकेय की प्रामाणिक पूजा विधि, षडानन मंत्र, मंगल शांति और स्कन्द षष्ठी व्रत पढ़ें।'
		],

		// ═══════════════════════════════════════════════════════════
		// 12. SURYA PUJA VIDHI (Ratha Saptami / Aditya Puja)
		// ═══════════════════════════════════════════════════════════
		[
			'slug'            => 'surya-puja-vidhi',
			'title'           => 'Surya Puja & Ratha Saptami Vidhi (సూర్య పూజ & రథసప్తమి)',
			'title_en'        => 'Surya Puja & Ratha Saptami Vidhanam',
			'title_te'        => 'సూర్య పూజా విధానం & రథసప్తమి పూజ',
			'title_hi'        => 'सूर्य पूजा एवं रथ सप्तमी विधान',
			'deity'           => 'Surya',
			'deity_slug'      => 'surya',
			'category'        => 'Festival Poojas',
			'duration'        => '45 Minutes',
			'intro_en'        => 'Lord Surya (the Sun God) is the Pratyaksha Daivam—the directly visible deity who bestows life, light, health, and consciousness upon all creation. Worshiping the Sun with Arghya, red flowers, and Aditya Hridaya Stotra cures eye troubles, skin diseases, depression, and promotes radiant leadership.',
			'intro_te'        => '"ఆరోగ్యం భాస్కరాదిచ్ఛేత్" అని శాస్త్ర వచనం. ప్రత్యక్ష దైవమైన సూర్య భగవానుడిని ప్రతిరోజూ ఉదయకాలంలో అర్ఘ్య ప్రదానంతో, మరియు మాఘ శుద్ధ సప్తమి (రథసప్తమి) నాడు జిల్లేడు ఆకుల స్నానంతో పూజించడం వల్ల సకల రోగాలు నశించి తేజస్సు, దీర్ఘాయువు లభిస్తాయి.',
			'intro_hi'        => 'भगवान सूर्य प्रत्यक्ष देवता हैं जिनकी नित्य किरणों से चराचर जगत जीवित है। रथ सप्तमी तथा रविवार को सूर्य देव को तांबे के पात्र से अर्घ्य देने, लाल पुष्प अर्पित करने और आदित्य हृदय स्तोत्र के पाठ से अपार तेज, स्वास्थ्य और यश की प्राप्ति होती है।',
			'samagri'         => "• Pure copper vessel (Ragi chembu / तांबे का लोटा) for Arghya\n• Red flowers, Red sandalwood paste (Rakta Chandan), Akshatas\n• Water mixed with a pinch of turmeric and kumkum\n• Seven Ekka / Calotropis leaves (జిల్లేడు ఆకులు / आक के पत्ते) for Ratha Saptami snanam\n• Cow milk and new raw rice for boiling outdoor Ksheerannam on cow-dung cake stove\n• Sugarcane pieces, Jaggery, Bananas\n• Camphor and brass bell",
			'preparation'     => 'Wake up before sunrise. On Ratha Saptami, place seven Calotropis leaves on the head, shoulders, and knees while taking a holy bath. Face East towards the rising sun.',
			'sankalpam'       => 'अद्य आदित्यपूजन शुभदिने... मम सकलरोगनिवारणार्थं, तेजः आरोग्य यशोऽभिवृद्ध्यर्थं प्रत्यक्षदेव श्री सूर्यनारायण प्रीतार्थं अर्घ्यप्रदानं पूजनं च करिष्ये ॥',
			'kalasha_sthapana' => 'Hold the copper vessel filled with water, red flowers, and akshatas directly before the Sun rays.',
			'avahanam'        => 'ॐ सूर्याय नमः । आदित्याय नमः । ॐ भूर्भुवः स्वः तत्सवितुर्वरेण्यं भर्गो देवस्य धीमहि धियो यो नः प्रचोदयात् ॥',
			'dhyana'          => 'रक्ताम्बुजासनमशेषगुणैकसिन्धुं भानुं समस्तजगतामधिपं भजामि । पद्मद्वयाभयवरान् दधतं कराब्जैर्माणिक्यमौलिमुरुहाररुचं विचित्रम् ॥',
			'main_puja'       => 'Offer three continuous streams of water (Arghya) through the index fingers and thumbs without stepping on the falling water. Chant the 12 names of Surya: Mitraya Namah, Ravaye Namah, Suryaya Namah, Bhanave Namah, Khagaya Namah, Pushne Namah, Hiranyagarbhaya Namah, Marichaye Namah, Adityaya Namah, Savitre Namah, Arkaya Namah, Bhaskaraya Namah.',
			'mantra_japa'     => 'Chant the Gayatri Mantra or Aditya Hridaya Stotram with devotion, looking towards the solar rays.',
			'naivedyam'       => 'Offer freshly prepared Ksheerannam (milk rice cooked under the open sky), jaggery, raw sugarcane, and fresh fruits.',
			'aarti'           => 'Wave pure camphor looking at the Sun and prostrate in Sashtanga Surya Namaskaram.',
			'prarthana'       => 'नमस्ते पद्मनाभाय नमस्ते पद्ममालिने । नमः सहस्रसूर्याय नमः प्रत्यक्षसाक्षिणे ॥',
			'prasadam'        => 'Partake of the solar-energized Ksheerannam and touch the blessed Arghya water to the forehead and eyes.',
			'visarjan'        => 'Turn around in a complete circle three times performing Atma Pradakshina.',
			'vrat_rules'      => 'Avoid consuming salt on Sundays or Ratha Saptami to achieve peak healing and purification benefits.',
			'faq'             => [
				[
					'q' => 'How should the water be poured while giving Surya Arghya?',
					'a' => 'Hold the copper kalash with both hands raised above forehead level so the morning sunlight filters through the water stream, and look at the Sun through the cascading water.'
				]
			],
			'related_mantras'  => [ 'surya-mantra', 'gayatri-mantra', 'aditya-hridaya-stotra' ],
			'related_festivals'=> [ 'ratha-saptami', 'makara-sankranti' ],
			'seo_title_en'     => 'Surya Puja & Ratha Saptami Vidhi: Arghya Procedure, 12 Names & Aditya Mantras',
			'seo_title_te'     => 'సూర్య పూజా విధానం & రథసప్తమి: అర్ఘ్య ప్రదానం, ద్వాదశ నామాలు, ఆదిత్య హృదయ స్తోత్రం',
			'seo_title_hi'     => 'सूर्य पूजा एवं रथ सप्तमी विधि: तांबे के लोटे से अर्घ्य, 12 सूर्य नाम एवं मंत्र',
			'seo_desc_en'      => 'Authentic Surya Puja Vidhanam with copper vessel Arghya rules, 12 Surya Namaskara mantras, Ratha Saptami Calotropis leaf bath, and health benefits at Dharma Jyothi Vedika.',
			'seo_desc_te'      => 'ధర్మ జ్యోతి వేదికలో సూర్య నమస్కారాలు, అర్ఘ్యం ఇచ్చే విధానం, జిల్లేడు ఆకుల రథసప్తమి స్నానం మరియు ఆదిత్య మంత్రాల సంపూర్ణ సమాచారం.',
			'seo_desc_hi'      => 'धर्मा ज्योति वेदिका पर भगवान सूर्य के अर्घ्य की संपूर्ण विधि, द्वादश नाम, रथ सप्तमी खीर भोग और आदित्य हृदय स्तोत्र पढ़ें।'
		]
	];
}
