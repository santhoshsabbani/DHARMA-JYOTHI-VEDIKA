/**
 * ============================================================
 * DJV Muhurtham Finder Application & Client Engine
 * website/assets/js/muhurtham-app.js
 *
 * Implements the 58 Event-Based Vedic Muhurtham Finder:
 * - Master event registry across 7 classical Vedic categories
 * - Dynamic event-specific form schemas (only relevant fields)
 * - Educational content display before form
 * - Location integration (Pan-India DB & Geolocation)
 * - Date range selection & quick expanders
 * - REST API call to /wp-json/djv/v1/muhurtham
 * - Astronomical client-side calculation fallback
 * - Multiple ranked results (Best, Very Good, Good, Acceptable)
 * - Transparent Vedic rule reasoning
 * - Side-by-side comparison modal (up to 3 options)
 * - Print & Share
 * - Multilingual support (English, Telugu, Hindi)
 * ============================================================
 */

(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.DJVMuhurthamApp = factory();
  }
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  // 58 Master Events with Multilingual Details and Classical Rules
  const EVENTS_DATA = {
  "marriage": {
    "slug": "marriage",
    "legacy_slug": "vivaha-muhurtham",
    "icon": "💍",
    "category": "marriage_relationships",
    "title": {
      "en": "Marriage / Vivaha Muhurtham",
      "te": "వివాహ ముహూర్తం (Marriage)",
      "hi": "विवाह मुहूर्त (Vivaha Sanskar)"
    },
    "short_desc": {
      "en": "Sacred wedding auspicious timings based on classical Vivaha Shastra.",
      "te": "శాస్త్రోక్త వివాహ సంస్కారానికి శ్రేష్ఠమైన శుభ ముహూర్తాల సమాచారం.",
      "hi": "वैदिक विवाह संस्कार हेतु शास्त्रोक्त शुभ मुहूर्त एवं लग्न विचार।"
    },
    "form_schema": "marriage",
    "content": {
      "about": {
        "en": "Marriage (Vivaha Sanskar) is the most sacred of the sixteen Shodasha Samskaras in Sanatana Dharma. It marks the holy union of two souls and their families, establishing Dharma, Artha, Kama, and Moksha in the Grihastha ashrama. Selecting an auspicious wedding Lagna ensures lasting affection, family harmony, health, and spiritual prosperity.",
        "te": "షోడశ సంస్కారాలలో వివాహ సంస్కారం అత్యంత ప్రధానమైనది. ఇద్దరు వ్యక్తులు, వారి కుటుంబాలు ధర్మ, అర్థ, కామ, మోక్షాల సాధనకై ఒక్కటయ్యే పవిత్ర బంధం ఇది. శుభ ముహూర్తంలో వివాహం జరిపించడం వలన దాంపత్య జీవితం సుఖసంతోషాలతో, ఆయురారోగ్యాలతో వర్ధిల్లుతుంది.",
        "hi": "षोडश संस्कारों में विवाह संस्कार गृहस्थ धर्म का आधार है। यह दो आत्माओं और दो कुलों का पवित्र मिलन है। शुभ मुहूर्त में विवाह करने से दांपत्य जीवन में सुख, शांति, वंश वृद्धि और अखंड सौभाग्य की प्राप्ति होती है।"
      },
      "religious_significance": {
        "en": "As expounded in Muhurtha Chintamani, Brihat Samhita, and Kalavidhana, the wedding lagna captures the cosmic moment of Panigrahana and Mangalasutra Dharana. An unblemished Lagna burns planetary afflictions and secures the divine grace of Sri Lakshmi Narayana.",
        "te": "ముహూర్త చింతామణి మరియు బృహత్ సంహితల ప్రకారం పాణిగ్రహణం మరియు మంగళసూత్ర ధారణ సమయంలో ఉండే లగ్నం జీవితాంతం ప్రభావం చూపుతుంది. శుభ లగ్నం సమస్త గ్రహదోషాలను నివారించి లక్ష్మీనారాయణుల అనుగ్రహాన్ని ప్రసాదిస్తుంది.",
        "hi": "मुहूर्त चिंतामणि एवं वृहत्संहिता के अनुसार पाणिग्रहण एवं मंगलसूत्र धारण के समय का लग्न नवदंपति के संपूर्ण जीवन को प्रभावित करता है।"
      },
      "why_muhurtham": {
        "en": "The planetary alignment at the marriage hour sets the astrological bedrock for the couple's joint journey. Astrological scrutiny avoids Kuja Dosha afflictions, Jamitra Dosha, and malefic combustion (Guru or Shukra Moudhya).",
        "te": "వివాహ సమయంలోని గ్రహ స్థితులు నూతన దంపతుల భవిష్యత్తుకు పునాది. శుభ నక్షత్రం, తిథి, నిర్దోష లగ్నాన్ని ఎన్నుకోవడం వలన జామిిత్ర దోషం, కుజ దోషాల ప్రభావం తగ్గి సుఖవంతమైన దాంపత్యం లభిస్తుంది.",
        "hi": "विवाह का समय दोनों के संयुक्त भाग्य को निर्धारित करता है। शुभ नक्षत्र, तिथि और जामित्र आदि दोषों से रहित लग्न का चयन दांपत्य में सामंजस्य सुनिश्चित करता है।"
      },
      "selection_principles": {
        "en": "Vivaha Muhurtham avoids 21 Mahadoshas including Jamitra Dosha, Vedha, Rikta tithis (4, 9, 14), Amavasya, eclipses, and combustion of Jupiter (Guru) or Venus (Shukra). Personalized mode evaluates Tarabala and Chandrabala.",
        "te": "వివాహ ముహూర్తంలో ముఖ్యంగా రిక్త తిథులు, అమావాస్య, గురు/శుక్ర మౌఢ్యాలు, జామిిత్ర దోషం పరిహరిస్తారు. వ్యక్తిగత విధానంలో వధూవరుల తారాబలం మరియు చంద్రబలం పరిశీలింపబడతాయి.",
        "hi": "विवाह मुहूर्त में गुरु-शुक्र अस्त, रिक्ता तिथियां, जामित्र दोष एवं वेध का त्याग किया जाता है। व्यक्तिगत गणना में ताराबल व चंद्रबल जाँचा जाता है।"
      },
      "required_details": {
        "en": "Bride and Groom names, ceremony location, date range, preferred time of day, and regional tradition. Optional birth details for personalized mode.",
        "te": "వధూవరుల పేర్లు, కళ్యాణ వేదిక స్థలం, తేదీల పరిధి, ప్రాధాన్య సమయం. వ్యక్తిగత ముహూర్తానికి ఇద్దరి పుట్టిన తేదీ, సమయం, స్థలం వివరాలు.",
        "hi": "वर एवं वधू का नाम, विवाह स्थल, दिनांक सीमा, समय प्राथमिकता। व्यक्तिगत गणना हेतु दोनों की जन्म तिथि, समय व स्थान।"
      }
    },
    "faqs": [
      {
        "q": {
          "en": "Why are Guru and Shukra Asta periods strictly avoided for marriage?",
          "te": "వివాహానికి గురు, శుక్ర మౌఢ్య కాలాలను ఎందుకు విడిచిపెడతారు?",
          "hi": "विवाह में गुरु और शुक्र अस्त का त्याग क्यों किया जाता है?"
        },
        "a": {
          "en": "Jupiter governs wisdom and marital longevity, while Venus signifies romance and harmony. When either is combust, auspicious energy is diminished.",
          "te": "గురువు జ్ఞానానికి, మాంగల్య సౌభాగ్యానికి కారకుడు; శుక్రుడు దాంపత్య సుఖానికి కారకుడు. వీరిద్దరూ అస్తమించినప్పుడు శుభ ఫలితాలు తగ్గుతాయి.",
          "hi": "गुरु सौभाग्य व ज्ञान तथा शुक्र दांपत्य सुख के कारक हैं। इनके अस्त रहने पर शुभ फल क्षीण हो जाते हैं।"
        }
      }
    ],
    "rules": {
      "favorable_nakshatras": [
        4,
        5,
        10,
        12,
        13,
        15,
        17,
        19,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        18,
        24,
        25
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        15,
        30,
        1,
        8
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti",
        "Vishkambha",
        "Atiganda",
        "Shoola",
        "Ganda",
        "Vyaghata",
        "Vajra",
        "Parigha"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "engagement": {
    "slug": "engagement",
    "legacy_slug": "engagement-muhurtham",
    "icon": "🤝",
    "category": "marriage_relationships",
    "title": {
      "en": "Engagement / Nischayathartham Muhurtham",
      "te": "నిశ్చితార్థ ముహూర్తం (Engagement / Nischay)",
      "hi": "सगाई / वाग्दान मुहूर्त (Engagement / Nischay)"
    },
    "short_desc": {
      "en": "Auspicious timings for formal betrothal and mutual commitment ceremonies.",
      "te": "వధూవరుల నిశ్చితార్థ తాంబూలాల మార్పిడికి శుభ ముహూర్తాలు.",
      "hi": "सगाई एवं वाग्दान संस्कार हेतु शुभ मुहूर्त।"
    },
    "form_schema": "engagement",
    "content": {
      "about": {
        "en": "Engagement (Nischayathartham, Vagdana, or Sagai) is the formal Vedic pledge where both families mutually consent and cement the marriage pact.",
        "te": "నిశ్చితార్థం అనేది వధూవరుల ఇరు కుటుంబాలు పరస్పరం తాంబూలాలు మార్చుకుని వివాహ బంధాన్ని ధృవీకరించే పవిత్ర వేడుక.",
        "hi": "वाग्दान या सगाई वह पवित्र संस्कार है जिसमें वर-वधू पक्ष विवाह का औपचारिक संकल्प लेते हैं।"
      },
      "religious_significance": {
        "en": "Invokes Lord Ganesha and Brihaspati as divine witnesses to the solemn commitment given by both families.",
        "te": "విఘ్నేశ్వరుడు మరియు బృహస్పతి సాక్షిగా జరిగే ఈ వేడుక సంబంధంలో స్థిరత్వాన్ని, నమ్మకాన్ని పెంపొందిస్తుంది.",
        "hi": "गणेश जी एवं बृहस्पति की साक्षी में वचनबद्ध होने से संबंध में प्रगाढ़ता आती है।"
      },
      "why_muhurtham": {
        "en": "Securing a pure astrological window averts unexpected obstacles and cements harmony between both extended families.",
        "te": "సరైన లగ్నాన్ని ఎన్నుకోవడం వలన అనుకోని ఆటంకాలు రాకుండా నిశ్చితార్థం ప్రశాంతంగా ముగుస్తుంది.",
        "hi": "शुभ समय का चयन किसी भी प्रकार के विघ्न या संशय को समाप्त करता है।"
      },
      "selection_principles": {
        "en": "Gentle and auspicious Nakshatras like Rohini, Hasta, Anuradha, and Swati are favored. Tuesday and Saturday are avoided.",
        "te": "రోహిణి, హస్త, అనురాధ, స్వాతి నక్షత్రాలు మరియు సోమ, బుధ, గురు, శుక్రవారాలు ఎంతో అనుకూలం.",
        "hi": "मृदु एवं शुभ नक्षत्र (रोहिणी, हस्त, अनुराधा) तथा रिक्ता तिथियों का त्याग प्रशस्त है।"
      },
      "required_details": {
        "en": "Bride and Groom names, engagement location, date range, and preferred time window.",
        "te": "వధూవరుల పేర్లు, నిశ్చితార్థ ప్రదేశం, తేదీల పరిధి, ప్రాధాన్య సమయం.",
        "hi": "वर एवं वधू का नाम, सगाई स्थल, दिनांक सीमा एवं समय प्राथमिकता।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        18,
        24
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "roka-ceremony": {
    "slug": "roka-ceremony",
    "legacy_slug": "roka-ceremony",
    "icon": "🌸",
    "category": "marriage_relationships",
    "title": {
      "en": "Roka Ceremony Muhurtham",
      "te": "రోకా వేడుక ముహూర్తం (Roka Ceremony)",
      "hi": "रोका संस्कार मुहूर्त (Roka Ceremony)"
    },
    "short_desc": {
      "en": "Auspicious timings for pre-engagement alliance sealing and family blessings.",
      "te": "వివాహ బంధపు ప్రాథమిక సమ్మతి మరియు కుటుంబ ఆశీర్వాదాల శుభ ముహూర్తాలు.",
      "hi": "विवाह पूर्व प्रथम औपचारिक स्वीकृति एवं शुभाशीर्वाद मुहूर्त।"
    },
    "form_schema": "roka_ceremony",
    "content": {
      "about": {
        "en": "The Roka ceremony marks the initial formal agreement between the bride's and groom's families, confirming the auspicious alliance.",
        "te": "రోకా వేడుక వివాహ సంబంధంలో ఇరు కుటుంబాల మధ్య జరిగే ప్రాథమిక అంగీకార సంబరం.",
        "hi": "रोका संस्कार वर और वधू पक्ष के मध्य संबंध की प्रथम औपचारिक सहमति का उत्सव है।"
      },
      "religious_significance": {
        "en": "Invokes Sri Lakshmi and Kula Devatas with sweets and coconut to bless the alliance with prosperity.",
        "te": "కులదేవతలను, లక్ష్మీదేవిని స్మరిస్తూ జరిపే ఈ వేడుక ఇరు కుటుంబాలకు మేలు కలిగిస్తుంది.",
        "hi": "कुलदेवता एवं लक्ष्मी जी की आराधना के साथ संबंध की नींव रखी जाती है।"
      },
      "why_muhurtham": {
        "en": "Starting on an auspicious Tithi ensures that all subsequent wedding planning unfolds peacefully.",
        "te": "శుభ తిథి మరియు వారంలో ప్రారంభించడం వలన భవిష్యత్తు సన్నాహాలు ఆనందంగా సాగుతాయి.",
        "hi": "शुभ तिथि में शुरुआत से आगे के सभी मांगलिक कार्य निर्विघ्न संपन्न होते हैं।"
      },
      "selection_principles": {
        "en": "Shukla Paksha is preferred. Rikta Tithis and Tuesday/Saturday are avoided.",
        "te": "శుక్ల పక్షం, అనుకూల నక్షత్రాలు, బుధ, గురు, శుక్రవారాలు ఎంతో శ్రేష్ఠమైనవి.",
        "hi": "शुक्ल पक्ष, सौम्य वार एवं शुभ नक्षत्र ग्राह्य हैं।"
      },
      "required_details": {
        "en": "Bride and Groom family names, venue location, date range, and preferred hours.",
        "te": "ఇరు కుటుంబాల పేర్లు, వేదిక స్థలం, తేదీల పరిధి, ప్రాధాన్య సమయం.",
        "hi": "परिवार के मुखिया का नाम, स्थल, दिनांक एवं समय।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "wedding-ceremonies": {
    "slug": "wedding-ceremonies",
    "legacy_slug": "wedding-ceremonies",
    "icon": "✨",
    "category": "marriage_relationships",
    "title": {
      "en": "Wedding-Related Ceremonies Muhurtham",
      "te": "వివాహ అనుబంధ వేడుకల ముహూర్తం (Haldi, Mehendi, Sangeet)",
      "hi": "विवाह संबंधी उत्सव मुहूर्त (हल्दी, मेहंदी, संगीत)"
    },
    "short_desc": {
      "en": "Auspicious timing for Haldi, Mehendi, Sangeet, and pre-wedding rituals.",
      "te": "హల్దీ, మెహందీ, మంగళస్నానం వంటి వివాహ పూర్వ శుభకార్యాల ముహూర్తాలు.",
      "hi": "हल्दी, मेहंदी, मंडप पूजा एवं विवाह पूर्व उत्सवों का शुभ मुहूर्त।"
    },
    "form_schema": "wedding_ceremonies",
    "content": {
      "about": {
        "en": "Pre-wedding celebrations such as Haldi (Mangalasnanam), Mehendi, and Mandapam Puja purify the couple and awaken celebratory vitality.",
        "te": "పెళ్లికి ముందు జరిగే హల్దీ, మెహందీ, మండప స్థాపన వంటి వేడుకలు ఎంతో ముఖ్యమైనవి.",
        "hi": "विवाह पूर्व हल्दी, मेहंदी, तेल-बाण एवं मंडपाच्छादन आदि मांगलिक उत्सव सकारात्मक ऊर्जा प्रदान करते हैं।"
      },
      "religious_significance": {
        "en": "Turmeric carries the divine blessing of Jupiter and the cleansing grace of Mother Parvati.",
        "te": "పసుపు సాక్షాత్తు పార్వతీదేవి స్వరూపం; శుభ సమయంలో పూయడం వలన దేహకాంతి, మనశ్శాంతి లభిస్తాయి.",
        "hi": "हल्दी माता पार्वती और गुरु का प्रतीक है।"
      },
      "why_muhurtham": {
        "en": "Prevents unforeseen health setbacks and shields against negative vibrations during celebrations.",
        "te": "ఉత్సవాలు ఎటువంటి ఆటంకాలు లేకుండా సంతోషంగా సాగడానికి ముహూర్తం దోహదపడుతుంది.",
        "hi": "उत्सव बिना किसी विघ्न के आनंदपूर्वक संपन्न हो, इसलिए मुहूर्त विचार आवश्यक है।"
      },
      "selection_principles": {
        "en": "Avoid Rahu Kalam and Yamagandam. Daytime hours with benefic planetary influence are chosen.",
        "te": "రాహుకాలం, దుర్ముహూర్తం లేని పగటి వేళలు శ్రేష్ఠం.",
        "hi": "राहुकाल एवं यमगंड से मुक्त शुभ होरा में अनुष्ठान करना चाहिए।"
      },
      "required_details": {
        "en": "Couple names, specific ceremony (Haldi, Mehendi, Sangeet, Mandap), venue city, and date range.",
        "te": "వధూవరుల పేర్లు, నిర్దిష్ట వేడుక పేరు, వేదిక నగరం, తేదీల పరిధి.",
        "hi": "वर-वधू का नाम, उत्सव का प्रकार, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "griha-pravesh": {
    "slug": "griha-pravesh",
    "legacy_slug": "gruhapravesham-muhurtham",
    "icon": "🏠",
    "category": "home_property",
    "title": {
      "en": "Griha Pravesh Muhurtham",
      "te": "గృహప్రవేశ ముహూర్తం (Griha Pravesh)",
      "hi": "गृह प्रवेश मुहूर्त (Griha Pravesh)"
    },
    "short_desc": {
      "en": "House warming auspicious timings for Apoorva, Sapoorva, and Dwandwa house entries.",
      "te": "నూతన గృహ ప్రవేశానికి శ్రేష్ఠమైన శుభ ముహూర్తాల సమాచారం.",
      "hi": "नये घर में प्रवेश हेतु शास्त्रोक्त गृह प्रवेश शुभ मुहूर्त।"
    },
    "form_schema": "griha_pravesh",
    "content": {
      "about": {
        "en": "Griha Pravesh is the sacred ceremony performed before dwelling in a new house. According to Matsya Purana and Vastu Shastra, entering during an auspicious cosmic hour blesses the residents with prosperity, health, and peace.",
        "te": "నూతన గృహంలోకి అడుగుపెట్టే ముందు చేసే పవిత్రమైన శాంతి కార్యక్రమం గృహప్రవేశం. వాస్తు శాస్త్ర నియమాల ప్రకారం శుభ ముహూర్తంలో ఇంట్లోకి ప్రవేశిస్తే లక్ష్మీదేవి కొలువై ఉంటుంది.",
        "hi": "गृह प्रवेश नए घर में रहने से पूर्व किया जाने वाला पवित्र संस्कार है।"
      },
      "religious_significance": {
        "en": "The ceremony invokes Vastu Purusha, Lord Ganesha, and Navagrahas. There are three traditional types: Apoorva (newly built), Sapoorva (migration/rental), and Dwandwa (renovation).",
        "te": "ఈ పూజలో వాస్తు పురుషుడు, విఘ్నేశ్వరుడు, నవగ్రహాల ఆరాధన జరుగుతుంది.",
        "hi": "इसमें वास्तु पुरुष, गणेश जी एवं नवग्रहों का आह्वान किया जाता है।"
      },
      "why_muhurtham": {
        "en": "A pure Lagna aligned with favorable lunar Nakshatras establishes enduring positive vibrations and drives away negative energies.",
        "te": "సరైన లగ్నంలో ప్రవేశించడం వల్ల ఇంట సిరిసంపదలు వర్ధిల్లుతాయి మరియు వాస్తు దోషాలు తొలగిపోతాయి.",
        "hi": "अनुकूल लग्न एवं नक्षत्र में गृह प्रवेश सुख-समृद्धि का कारक बनता है।"
      },
      "selection_principles": {
        "en": "Fixed signs (Sthira Lagna: Taurus, Leo, Scorpio, Aquarius) are preferred for long-lasting stability. Rikta tithis, Amavasya, and Tuesday are strictly avoided.",
        "te": "స్థిర లగ్నాలలో (వృషభ, సింహ, వృశ్చిక, కుంభ) గృహప్రవేశం అత్యంత శ్రేష్ఠం. మంగళవారం, రిక్త తిథులు, అమావాస్య నిషిద్ధం.",
        "hi": "गृह प्रवेश के लिए स्थिर लग्न को सर्वोत्तम माना जाता है। मंगलवार, रिक्ता तिथियां और अमावस्या वर्जित हैं।"
      },
      "required_details": {
        "en": "Owner name, house location, house type, date range, and optional Vastu preferences.",
        "te": "యజమాని పేరు, గృహ స్థలం, ఇల్లు రకం, తేదీల పరిధి, వాస్తు ప్రాధాన్యతలు.",
        "hi": "गृहस्वामी का नाम, मकान का पता, प्रकार, दिनांक सीमा एवं वास्तु प्राथमिकता।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        4,
        5,
        8,
        12,
        14,
        17,
        21,
        22,
        23,
        24,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        10,
        16,
        18,
        19
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        15,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5
      ],
      "prohibited_weekdays": [
        0,
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "bhoomi-pujan": {
    "slug": "bhoomi-pujan",
    "legacy_slug": "bhoomi-pujan",
    "icon": "🏗️",
    "category": "home_property",
    "title": {
      "en": "Bhoomi Pujan Muhurtham",
      "te": "భూమి పూజ ముహూర్తం (Bhoomi Pujan)",
      "hi": "भूमि पूजन मुहूर्त (Bhoomi Pujan)"
    },
    "short_desc": {
      "en": "Auspicious foundation laying and groundbreaking timings for plots and construction.",
      "te": "భవన నిర్మాణానికి శంకుస్థాపన, భూమి పూజ శుభ ముహూర్తాలు.",
      "hi": "भवन निर्माण हेतु शुभ नींव खनन एवं भूमि पूजन मुहूर्त।"
    },
    "form_schema": "bhoomi_pujan",
    "content": {
      "about": {
        "en": "Bhoomi Pujan is the foundation stone laying ritual conducted before commencing construction. Mother Earth (Bhudevi) and Vastu Purusha are propitiated.",
        "te": "నూతన నిర్మాణాన్ని ప్రారంభించే ముందు భూమాతకు, వాస్తు పురుషునికి భక్తితో చేసే పూజ భూమి పూజ.",
        "hi": "भूमि पूजन निर्माण कार्य प्रारंभ करने से पूर्व भूदेवी एवं वास्तु पुरुष की विधिवत आराधना है।"
      },
      "religious_significance": {
        "en": "Honors the five elements (Pancha Bhootas). Laying the cornerstone during favorable planetary horas protects construction workers.",
        "te": "పంచభూతాలను శాంతింపజేసి, వాస్తు దోషాలను నివారించడానికి శంకుస్థాపన ముహూర్తం ఎంతో అవసరం.",
        "hi": "पंचभूतों की शुद्धि एवं वास्तु दोष निवारण हेतु शुभ मुहूर्त में नींव रखना फलदायी होता है।"
      },
      "why_muhurtham": {
        "en": "Starting excavation under favorable planetary alignments averts legal hurdles and structural stalls.",
        "te": "అనుకూల లగ్నం మరియు నక్షత్రంలో ప్రారంభించిన నిర్మాణం శీఘ్రంగా, సకల శుభాలతో పూర్తవుతుంది.",
        "hi": "शुभ मुहूर्त में आरंभ किया गया निर्माण कार्य धन, यश और स्थिरता प्रदान करता है।"
      },
      "selection_principles": {
        "en": "Shukla Paksha is favored. Tuesday is avoided. Favorable nakshatras include Rohini, Mrigashira, Uttara Phalguni, Chitra, and Anuradha.",
        "te": "శుక్ల పక్షం, స్థిర లగ్నాలు, రోహిణి, మృగశిర, అనురాధ నక్షత్రాలు ఎంతో ప్రశస్తమైనవి.",
        "hi": "शुक्ल पक्ष, स्थिर लग्न एवं शुभ नक्षत्रों में भूमि खनन कार्य प्रशस्त माना गया है।"
      },
      "required_details": {
        "en": "Owner name, property location, property type, date range, and optional birth details.",
        "te": "యజమాని పేరు, స్థలం చిరునామా, ప్రాపర్టీ రకం, అనువైన తేదీల పరిధి.",
        "hi": "भूस्वामी का नाम, स्थल का पता, संपत्ति का प्रकार, तिथि सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        4,
        5,
        12,
        13,
        14,
        15,
        17,
        21,
        22,
        23,
        24,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        10,
        18,
        19
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        6,
        7,
        10,
        11,
        12,
        13,
        15
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "shilanyas": {
    "slug": "shilanyas",
    "legacy_slug": "shilanyas",
    "icon": "🧱",
    "category": "home_property",
    "title": {
      "en": "Foundation Ceremony / Shilanyas Muhurtham",
      "te": "శంకుస్థాపన ముహూర్తం (Shilanyas / Foundation)",
      "hi": "शिलान्यास मुहूर्त (Foundation Stone Ceremony)"
    },
    "short_desc": {
      "en": "Sacred foundation cornerstone installation auspicious timings.",
      "te": "పునాది రాయి వేయడానికి, శాస్త్రోక్త శంకుస్థాపనకు శ్రేష్ఠమైన ముహూర్తాలు.",
      "hi": "भवन की प्रथम शिला रखने एवं शिलान्यास संस्कार का शुभ मुहूर्त।"
    },
    "form_schema": "shilanyas",
    "content": {
      "about": {
        "en": "Shilanyas (Shanku Sthapana) is the sacred installation of the first consecrated stones and metals in the foundation trench.",
        "te": "భవన నిర్మాణంలో తొలి రాయిని (శంకును) శాస్త్రోక్తంగా ప్రతిష్టించే పరమ పవిత్ర కార్యక్రమం శంకుస్థాపన.",
        "hi": "शिलान्यास भवन निर्माण में प्रथम पूजित शिला को नींव में स्थापित करने का वैदिक संस्कार है।"
      },
      "religious_significance": {
        "en": "Invokes Kurma Avatar who supports the earth, ensuring immovable stability for generations.",
        "te": "ఈ పూజలో కూర్మ భగవానుడిని పూజించి భవనానికి అచంచల స్థిరత్వాన్ని కోరతారు.",
        "hi": "कूर्म भगवान एवं वास्तु पुरुष की स्थापना से भवन में सदियों तक स्थिरता रहती है।"
      },
      "why_muhurtham": {
        "en": "Prevents structural flaws, foundation settling, and abandoned projects.",
        "te": "నిర్మాణం మధ్యలో ఆగిపోకుండా సంపూర్ణంగా పూర్తయ్యేందుకు దోషరహిత లగ్నం అవసరం.",
        "hi": "भवन निर्माण बिना किसी रुकावट के पूर्ण हो, इसलिए मुहूर्त अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Sthira Lagna is mandatory. Solar movement in Uttarayana is especially preferred.",
        "te": "స్థిర లగ్నం, ఉత్తరాయణ పుణ్యకాలం అత్యంత శుభప్రదం.",
        "hi": "स्थिर लग्न एवं उत्तरायण काल शिलान्यास हेतु सर्वोत्तम है।"
      },
      "required_details": {
        "en": "Builder/owner name, cornerstone orientation, property address, and date range.",
        "te": "యజమాని పేరు, శంకు స్థాపించే దిశ, స్థలం వివరాలు, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, शिलान्यास की दिशा, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "start-construction": {
    "slug": "start-construction",
    "legacy_slug": "start-construction",
    "icon": "🚜",
    "category": "home_property",
    "title": {
      "en": "Start Construction Muhurtham",
      "te": "నిర్మాణ పనుల ప్రారంభ ముహూర్తం (Start Construction)",
      "hi": "निर्माण कार्य आरंभ मुहूर्त (Start Construction)"
    },
    "short_desc": {
      "en": "Auspicious timings for commencing active civil and structural construction work.",
      "te": "భవన నిర్మాణ సివిల్ పనులు, పిల్లర్లు ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "सक्रिय निर्माण, पिलर एवं कंक्रीट कार्य प्रारंभ करने का शुभ मुहूर्त।"
    },
    "form_schema": "start_construction",
    "content": {
      "about": {
        "en": "Commencing active civil construction—such as pillar casting and brickwork—under auspicious planetary rays ensures smooth work progression and worker safety.",
        "te": "భవనం పిల్లర్లు, శ్లాబ్ పనులు ప్రారంభించడానికి శాస్త్రోక్త సమయాన్ని ఎంచుకుంటారు.",
        "hi": "भवन के स्तंभ, दीवार एवं मुख्य निर्माण कार्य आरंभ करने का शुभ समय।"
      },
      "religious_significance": {
        "en": "Propitiates Lord Vishwakarma, the divine architect of the universe.",
        "te": "సకల శిల్పకళాధిపతియైన విశ్వకర్మ భగవానుని అనుగ్రహం లభిస్తుంది.",
        "hi": "शिल्पकार भगवान विश्वकर्मा का आशीर्वाद प्राप्त होता है।"
      },
      "why_muhurtham": {
        "en": "Prevents construction accidents, contractor conflicts, and sudden material delays.",
        "te": "పనుల్లో ప్రమాదాలు జరగకుండా, సామగ్రి కొరత రాకుండా ఉండేందుకు శుభ ముహూర్తం ముఖ్యం.",
        "hi": "दुर्घटनाओं से बचाव और सामग्री की निर्बाध आपूर्ति हेतु मुहूर्त विचार किया जाता है।"
      },
      "selection_principles": {
        "en": "Avoid Mars afflicted days. Pushya, Rohini, Hasta, and Shravana are favorable.",
        "te": "పుష్యమి, రోహిణి, హస్త, శ్రవణం నక్షత్రాలు ఎంతో అనుకూలం.",
        "hi": "पुष्य, रोहिणी, हस्त एवं श्रवण नक्षत्र ग्राह्य हैं।"
      },
      "required_details": {
        "en": "Owner/contractor name, construction scale, location, and date range.",
        "te": "యజమాని పేరు, నిర్మాణ పరిమాణం, స్థలం, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, निर्माण स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "start-renovation": {
    "slug": "start-renovation",
    "legacy_slug": "start-renovation",
    "icon": "🔨",
    "category": "home_property",
    "title": {
      "en": "Start Renovation Muhurtham",
      "te": "గృహ పునర్నిర్మాణ ముహూర్తం (Start Renovation)",
      "hi": "गृह नवीनीकरण मुहूर्त (Start Renovation)"
    },
    "short_desc": {
      "en": "Auspicious timings for structural remodeling, interior renovations, and roof repairs.",
      "te": "ఇంటి ఆధునికీకరణ, ఇంటీరియర్ పనులు, మరమ్మతులు ప్రారంభించడానికి ముహూర్తాలు.",
      "hi": "घर की मरम्मत, रंग-रोगन एवं नवीनीकरण कार्य का शुभ मुहूर्त।"
    },
    "form_schema": "start_renovation",
    "content": {
      "about": {
        "en": "Starting house remodeling or major interior alterations during an auspicious Muhurtham restores positive Vastu harmony and avoids disturbing home energy fields.",
        "te": "పాత ఇంటిని ఆధునీకరించేటప్పుడు లేదా ఇంటీరియర్ చేసేటప్పుడు వాస్తు దోషాలు కలగకుండా ఈ ముహూర్తం ఎంచుకుంటారు.",
        "hi": "घर का नवीनीकरण या बदलाव करते समय वास्तु ऊर्जा संतुलित रखने हेतु शुभ मुहूर्त में काम शुरू करें।"
      },
      "religious_significance": {
        "en": "Vastu Shanti prayer pacifies deities residing in established structures.",
        "te": "గృహంలో కొలువైన శక్తులను శాంతింపజేసి మరమ్మతులు సాఫీగా జరిగేలా చూస్తుంది.",
        "hi": "वास्तु शांति से घर में सकारात्मक ऊर्जा का पुनर्संचार होता है।"
      },
      "why_muhurtham": {
        "en": "Prevents renovation costs from spiraling out of control and averts unforeseen damage.",
        "te": "అదనపు ఖర్చులు పెరగకుండా అనుకున్న బడ్జెట్‌లో పనులు పూర్తి కావడానికి ముహూర్తం ముఖ్యం.",
        "hi": "बजट नियंत्रण और कार्य की समय पर समाप्ति हेतु शुभ मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Shukla Paksha and benefic weekdays (Wed, Thu, Fri) are preferred.",
        "te": "బుధ, గురు, శుక్రవారాలు, శుక్ల పక్షం అనుకూలం.",
        "hi": "शुक्ल पक्ष एवं शुभ वारों में नवीनीकरण कार्य प्रारंभ करें।"
      },
      "required_details": {
        "en": "Owner name, renovation scope, home address, and date range.",
        "te": "యజమాని పేరు, మరమ్మతుల రకం, ఇంటి చిరునామా, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, कार्य का प्रकार, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "property-purchase": {
    "slug": "property-purchase",
    "legacy_slug": "property-purchase",
    "icon": "🏡",
    "category": "home_property",
    "title": {
      "en": "Property Purchase Muhurtham",
      "te": "ఆస్తి కొనుగోలు ముహూర్తం (Property Purchase)",
      "hi": "संपत्ति क्रय मुहूर्त (Property Purchase)"
    },
    "short_desc": {
      "en": "Auspicious timing for buying residential plots, agricultural lands, flats, and estates.",
      "te": "స్థలాలు, ఫ్లాట్లు, వ్యవసాయ భూములు కొనుగోలు చేయడానికి శుభ ముహూర్తాలు.",
      "hi": "भूमि, भवन, फ्लैट अथवा कृषि भूमि खरीदने का शुभ मुहूर्त।"
    },
    "form_schema": "property_purchase",
    "content": {
      "about": {
        "en": "Purchasing real estate or land is a life-defining milestone. Selecting an auspicious cosmic hour ensures the property brings prosperity, value appreciation, and generational peace.",
        "te": "భూమి లేదా భవనం కొనుగోలు చేయడం జీవితంలో పెద్ద ఘట్టం. సరైన ముహూర్తంలో కొంటే ఆస్తి వృద్ధి చెంది శాంతి కలుగుతుంది.",
        "hi": "भूमि या भवन क्रय करना जीवन का बड़ा निर्णय है। शुभ काल में क्रय करने से संपत्ति फलदायी और लाभदायक होती है।"
      },
      "religious_significance": {
        "en": "Propitiates Bhudevi and Lord Kuvera to bestow enduring abundance upon the buyer.",
        "te": "భూమాత, కుబేరుల అనుగ్రహం లభించి సంపద స్థిరంగా ఉంటుంది.",
        "hi": "भूदेवी एवं कुबेर देव की कृपा से संपत्ति में निरंतर वृद्धि होती है।"
      },
      "why_muhurtham": {
        "en": "Shields against fraudulent titles, encumbrances, and hidden legal pitfalls.",
        "te": "భవిష్యత్తులో స్థల వివాదాలు, మోసాలు జరగకుండా కాపాడుతుంది.",
        "hi": "विवादित संपत्ति, कानूनी अड़चनों एवं धोखाधड़ी से रक्षा होती है।"
      },
      "selection_principles": {
        "en": "Mrigashira, Punarvasu, Magha, Visakha, Anuradha, and Revati are favored. Thursday and Friday are best.",
        "te": "మృగశిర, పునర్వసు, అనూరాధ, రేవతి నక్షత్రాలు మరియు గురు, శుక్రవారాలు శ్రేష్ఠం.",
        "hi": "मृगशिरा, पुनर्वसु, अनुराधा, रेवती नक्षत्र एवं गुरुवार-शुक्रवार उत्तम हैं।"
      },
      "required_details": {
        "en": "Buyer name, property category (plot, flat, land), location, and date range.",
        "te": "కొనుగోలుదారు పేరు, ఆస్తి రకం, ప్రదేశం, తేదీల పరిధి.",
        "hi": "क्रेता का नाम, संपत्ति का प्रकार, स्थान एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "property-registration": {
    "slug": "property-registration",
    "legacy_slug": "property-registration",
    "icon": "📜",
    "category": "home_property",
    "title": {
      "en": "Property Registration Muhurtham",
      "te": "ఆస్తి రిజిస్ట్రేషన్ ముహూర్తం (Property Registration)",
      "hi": "संपत्ति रजिस्ट्री मुहूर्त (Property Registration)"
    },
    "short_desc": {
      "en": "Government registry and legal document signing auspicious timings.",
      "te": "రిజిస్ట్రేషన్ కార్యాలయంలో దస్తావేజులు రిజిస్టర్ చేయడానికి శుభ ముహూర్తాలు.",
      "hi": "भूमि व भवन की रजिस्ट्री कराने हेतु शुभ मुहूर्त।"
    },
    "form_schema": "property_registration",
    "content": {
      "about": {
        "en": "Signing official deeds during an auspicious hour protects legal title rights and shields against future litigation.",
        "te": "శుభ సమయంలో దస్తావేజులపై సంతకం చేస్తే చట్టపరమైన ఇబ్బందులు రావు.",
        "hi": "शुभ समय में रजिस्ट्री कराने से स्वामित्व सुरक्षित और निर्विवाद रहता है।"
      },
      "religious_significance": {
        "en": "Invokes Jupiter and Mercury for truthfulness, legality, and undisputed ownership title.",
        "te": "గురు, బుధుల అనుగ్రహంతో హక్కులు శాశ్వతంగా భద్రంగా ఉంటాయి.",
        "hi": "बृहस्पति और बुध की कृपा से संपत्ति पर अधिकार निष्कंटक रहता है।"
      },
      "why_muhurtham": {
        "en": "Ensures the title deed remains permanent, clear, and conflict-free.",
        "te": "భవిష్యత్తులో ఆస్తిపై ఎటువంటి కోర్టు వివాదాలు తలెత్తకుండా ఉంటుంది.",
        "hi": "भविष्य में संपत्ति संबंधी विवादों से मुक्ति मिलती है।"
      },
      "selection_principles": {
        "en": "Registrar office working hours are carved cleanly, avoiding Rahu Kalam and Gulika Kalam.",
        "te": "కార్యాలయ వేళలలో రాహుకాలం లేని సమయాన్ని ఎంచుకుంటారు.",
        "hi": "कार्यालयी समय के अनुसार राहुकाल रहित शुभ काल का चयन किया जाता है।"
      },
      "required_details": {
        "en": "Buyer details, registry location, and date range.",
        "te": "కొనుగోలుదారు వివరాలు, రిజిస్ట్రీ స్థలం, తేదీల పరిధి.",
        "hi": "क्रेता का विवरण एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "move-new-home": {
    "slug": "move-new-home",
    "legacy_slug": "move-new-home",
    "icon": "📦",
    "category": "home_property",
    "title": {
      "en": "Move Into New Home Muhurtham",
      "te": "కొత్త ఇంటికి మారే ముహూర్తం (Move Into Home)",
      "hi": "नए घर में शिफ्टिंग मुहूर्त (Move Into New Home)"
    },
    "short_desc": {
      "en": "Auspicious timings for shifting household belongings and moving into a rented home.",
      "te": "అద్దె ఇంటికి మారడానికి, వస్తువులు తరలించడానికి శుభ ముహూర్తాలు.",
      "hi": "किराए के मकान अथवा नए आवास में सामान शिफ्ट करने का शुभ मुहूर्त।"
    },
    "form_schema": "move_new_home",
    "content": {
      "about": {
        "en": "Shifting household items and sleeping the first night in a new rented dwelling requires an auspicious cosmic window to invite peace and harmony.",
        "te": "నూతన అద్దె గృహంలోకి వస్తువులను తరలించడానికి మరియు తొలి రాత్రి నివసించడానికి ఈ ముహూర్తం చూస్తారు.",
        "hi": "किराए के नए घर में सामान ले जाने और प्रथम निवास हेतु शुभ मुहूर्त देखा जाता है।"
      },
      "religious_significance": {
        "en": "Boiling milk ritual invokes Goddess Annapurna and Lakshmi for unending nourishment.",
        "te": "పాలు పొంగించే కార్యక్రమం అన్నపూర్ణాదేవి అనుగ్రహాన్ని కలిగిస్తుంది.",
        "hi": "दूध उबालने का संस्कार अन्नपूर्णा देवी की कृपा लाता है।"
      },
      "why_muhurtham": {
        "en": "Prevents health issues, sleeplessness, and landlord-tenant friction.",
        "te": "కుటుంబంలో మనశ్శాంతి మరియు ఆరోగ్య రక్షణకు దోహదపడుతుంది.",
        "hi": "परिवार में सुख-शांति एवं स्वास्थ्य रक्षा हेतु शुभ समय आवश्यक है।"
      },
      "selection_principles": {
        "en": "Avoid Amavasya, Rikta Tithis, and Tuesdays.",
        "te": "అమావాస్య, రిక్త తిథులు, మంగళవారం నివారిస్తారు.",
        "hi": "अमावस्या और रिक्ता तिथियों का त्याग किया जाता है।"
      },
      "required_details": {
        "en": "Resident name, move type, destination city, and date range.",
        "te": "నివాసి పేరు, మారే రకం, గమ్యస్థానం, తేదీల పరిధి.",
        "hi": "नाम, आवास का प्रकार, स्थान एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "namakaran": {
    "slug": "namakaran",
    "legacy_slug": "namakaranam-muhurtham",
    "icon": "👶",
    "category": "baby_family",
    "title": {
      "en": "Namakaran Muhurtham",
      "te": "నామకరణ ముహూర్తం (Namakaran)",
      "hi": "नामकरण संस्कार मुहूर्त (Namakaran)"
    },
    "short_desc": {
      "en": "Sacred naming ceremony auspicious timings for newborn boys and girls.",
      "te": "శిశువు నామకరణ సంస్కారానికి శ్రేష్ఠమైన శుభ ముహూర్తాలు.",
      "hi": "नवजात शिशु के नामकरण संस्कार हेतु शास्त्रोक्त शुभ मुहूर्त।"
    },
    "form_schema": "namakaran",
    "content": {
      "about": {
        "en": "Namakaran is the fifth Shodasha Samskara in which the newborn receives their formal name based on birth Nakshatra Pada syllables (Namakshara). An auspicious hour blesses the infant with fame, intelligence, and longevity.",
        "te": "జన్మించిన శిశువుకు జన్మ నక్షత్ర పాదాన్ని బట్టి పేరు పెట్టే పవిత్ర సంస్కారం నామకరణం. శుభ ముహూర్తంలో పేరు పెట్టడం వలన ఆయువు, కీర్తి, జ్ఞానం వృద్ధి చెందుతాయి.",
        "hi": "नामकरण षोडश संस्कारों में अत्यंत महत्वपूर्ण है। जन्म नक्षत्र के चरणानुसार अक्षर से शुभ मुहूर्त में नाम रखने से शिशु को दीर्घायु, यश और बुद्धि प्राप्त होती है।"
      },
      "religious_significance": {
        "en": "Invokes Surya, Agni, and Prajapati. The father whispers the sacred secret name into the infant's right ear.",
        "te": "ఈ వేడుకలో సూర్యుడు, అగ్నిదేవుడు, బ్రహ్మల ఆశీస్సులు కోరుతూ తండ్రి బిడ్డ కుడి చెవిలో పేరు చెబుతారు.",
        "hi": "पिता शिशु के दाहिने कान में प्रथम बार नाम का उच्चारण कर देवों का आशीर्वाद लेते हैं।"
      },
      "why_muhurtham": {
        "en": "Aligns the child's physical life with cosmic energies, ensuring auspicious vibrations each time their name is spoken.",
        "te": "పేరు పిలిచిన ప్రతిసారీ మంచి ప్రకంపనలు కలగడానికి శుభ తిథి, నక్షత్రాలు ఎంతో అవసరం.",
        "hi": "जीवनपर्यंत नाम की सकारात्मक ऊर्जा बनी रहे, इसलिए शुद्ध लग्न में नामकरण किया जाता है।"
      },
      "selection_principles": {
        "en": "Conducted on the 10th, 11th, 12th, or 16th day after birth. Anuradha, Rohini, Uttara Phalguni, Uttara Ashadha, Uttara Bhadrapada, Pushya, and Revati are favored.",
        "te": "జన్మించిన 11, 12, లేదా 16వ రోజున స్థిర లేదా మృదు నక్షత్రాలలో నిర్వహించడం శ్రేష్ఠం.",
        "hi": "जन्म के 11वें, 12वें अथवा 16वें दिन स्थिर एवं सौम्य नक्षत्रों में संस्कार किया जाता है।"
      },
      "required_details": {
        "en": "Baby date of birth, birth time, birth place, parents' names, ceremony location, and date range.",
        "te": "శిశువు పుట్టిన తేదీ, సమయం, స్థలం, తల్లిదండ్రుల పేర్లు, వేదిక నగరం, తేదీల పరిధి.",
        "hi": "शिशु की जन्म तिथि, समय, स्थान, माता-पिता का नाम एवं दिनांक সীমা।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        7,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        18,
        24
      ],
      "favorable_tithis": [
        1,
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "annaprashana": {
    "slug": "annaprashana",
    "legacy_slug": "annaprashana",
    "icon": "🥣",
    "category": "baby_family",
    "title": {
      "en": "Annaprashana Muhurtham",
      "te": "అన్నప్రాశన ముహూర్తం (Annaprashana)",
      "hi": "अन्नप्राशन मुहूर्त (First Feeding)"
    },
    "short_desc": {
      "en": "Auspicious timing for the baby's first feeding of solid food and grain.",
      "te": "శిశువుకు తొలిసారి ఘనాహారం, అన్నం తినిపించే పవిత్ర ముహూర్తాలు.",
      "hi": "शिशु को प्रथम बार अन्न ग्रहण कराने का पावन मुहूर्त।"
    },
    "form_schema": "annaprashana",
    "content": {
      "about": {
        "en": "Annaprashana marks the child's transition from milk to solid grain food. Usually performed in even months (6th, 8th) for male infants and odd months (5th, 7th) for female infants.",
        "te": "శిశువుకు మొట్టమొదటిసారి అన్నం తినిపించే సంస్కారం అన్నప్రాశన. మగపిల్లలకు సరి మాసాలలో (6, 8వ నెల), ఆడపిల్లలకు బేసి మాసాలలో (5, 7వ నెల) జరిపిస్తారు.",
        "hi": "शिशु को प्रथम बार दुग्ध के अतिरिक्त अन्न (खीर) खिलाने का संस्कार अन्नप्राशन कहलाता है। बालक हेतु सम मास तथा बालिका हेतु विषम मास प्रशस्त हैं।"
      },
      "religious_significance": {
        "en": "Mother Annapurna is invoked to ensure the child never suffers from hunger or digestive ailments.",
        "te": "అన్నపూర్ణాదేవి అనుగ్రహం వల్ల బిడ్డకు ఆయురారోగ్యాలు, తేజస్సు చేకూరుతాయి.",
        "hi": "माता अन्नपूर्णा की कृपा से शिशु का पाचन सुदृढ़ और स्वास्थ्य उत्तम रहता है।"
      },
      "why_muhurtham": {
        "en": "Safeguards the infant's developing digestive constitution and bestows physical vitality.",
        "te": "సరైన ముహూర్తంలో ఆహారం ఇవ్వడం వల్ల జీర్ణశక్తి బాగుండి రోగనిరోధక శక్తి పెరుగుతుంది.",
        "hi": "शिशु की शारीरिक पुष्टि और दीर्घायु हेतु शुभ काल का चयन किया जाता है।"
      },
      "selection_principles": {
        "en": "Avoid Rikta Tithis and Amavasya. Ashwini, Rohini, Mrigashira, Pushya, Hasta, Anuradha, and Revati are favored.",
        "te": "రోహిణి, మృగశిర, పుష్యమి, హస్త, అనురాధ నక్షత్రాలు మరియు శుక్ల పక్షం శ్రేష్ఠం.",
        "hi": "रिक्ता तिथियां त्याज्य हैं; रोहिणी, पुष्य, हस्त, अनुराधा नक्षत्र उत्तम हैं।"
      },
      "required_details": {
        "en": "Baby DOB, gender, parents' names, ceremony venue, and date range.",
        "te": "శిశువు పుట్టిన తేదీ, లింగం, తల్లిదండ్రుల పేర్లు, వేదిక, తేదీల పరిధి.",
        "hi": "शिशु का विवरण, लिंग, माता-पिता का नाम एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "mundan": {
    "slug": "mundan",
    "legacy_slug": "mundan",
    "icon": "✂️",
    "category": "baby_family",
    "title": {
      "en": "Mundan / Chudakarana Muhurtham",
      "te": "పుట్టువెంట్రుకల ముహూర్తం (Mundan / Chudakarana)",
      "hi": "मुंडन / चूड़ाकर्म संस्कार मुहूर्त (Mundan)"
    },
    "short_desc": {
      "en": "Auspicious timing for the child's first head-shaving tonsure ceremony.",
      "te": "పిల్లల మొదటి పుట్టువెంట్రుకలు తీయడానికి శ్రేష్ఠమైన ముహూర్తాలు.",
      "hi": "शिशु के प्रथम केश मुंडन एवं चूड़ाकर्म का शुभ मुहूर्त।"
    },
    "form_schema": "mundan",
    "content": {
      "about": {
        "en": "Mundan (Chudakarana or Chaula) is the eighth Vedic samskara where the child's birth hair is shaved, symbolizing purification and the shedding of past life traits.",
        "te": "శిశువు పుట్టువెంట్రుకలను తీసి పవిత్రం చేసే సంస్కారం చూడాకరణం. ఇది పూర్వజన్మల దోషాలను తొలగించి బుద్ధి వికాసానికి దారితీస్తుంది.",
        "hi": "चूड़ाकर्म संस्कार में शिशु के जन्मकालीन केशों को उतारकर शुद्धि की जाती है। यह बुद्धि और स्वास्थ्य वृद्धि हेतु अत्यंत आवश्यक है।"
      },
      "religious_significance": {
        "en": "Often performed at sacred tirthas (Tirupati, Varanasi, Haridwar) to offer devotion to deities.",
        "te": "తిరుపతి లేదా గంగాతీరం వంటి పవిత్ర పుణ్యక్షేత్రాలలో భక్తితో ఈ వేడుకను నిర్వహిస్తారు.",
        "hi": "तीर्थ क्षेत्रों में मुंडन कराकर बालक की दीर्घायु की प्रार्थना की जाती है।"
      },
      "why_muhurtham": {
        "en": "Protects the child from cranial health issues and stimulates cerebral development.",
        "te": "శాస్త్రోక్త కాలంలో చేయడం వలన తలభాగం చల్లబడి మేధాశక్తి పెరుగుతుంది.",
        "hi": "शिशु के बौद्धिक विकास और स्वास्थ्य रक्षा हेतु शुभ समय आवश्यक है।"
      },
      "selection_principles": {
        "en": "Performed in the 1st, 3rd, or 5th year. Uttarayana is preferred. Avoid elder child tonsure in Jyeshtha month.",
        "te": "1, 3 లేదా 5వ సంవత్సరంలో ఉత్తరాయణ పుణ్యకాలంలో చేయడం ఉత్తమం.",
        "hi": "प्रथम, तृतीय अथवा पंचम वर्ष में उत्तरायण काल में मुंडन प्रशस्त है।"
      },
      "required_details": {
        "en": "Child name, DOB, target age year, pilgrimage venue or city, and date range.",
        "te": "పిల్లవాడి పేరు, పుట్టిన తేదీ, వయస్సు, క్షేత్రం/నగరం, తేదీల పరిధి.",
        "hi": "शिशु का नाम, जन्म तिथि, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "karnavedha": {
    "slug": "karnavedha",
    "legacy_slug": "karnavedha",
    "icon": "👂",
    "category": "baby_family",
    "title": {
      "en": "Karnavedha Muhurtham",
      "te": "చెవులు కుట్టే ముహూర్తం (Karnavedha)",
      "hi": "कर्णवेध संस्कार मुहूर्त (Ear Piercing)"
    },
    "short_desc": {
      "en": "Auspicious timings for the sacred ear piercing ceremony of children.",
      "te": "శిశువులకు శాస్త్రోక్తంగా చెవులు కుట్టించడానికి శుభ ముహూర్తాలు.",
      "hi": "शिशु के कर्णवेध संस्कार हेतु शुभ मुहूर्त।"
    },
    "form_schema": "karnavedha",
    "content": {
      "about": {
        "en": "Karnavedha is the piercing of the child's earlobes, promoting acupuncture channels for mental acuity and spiritual hearing.",
        "te": "పిల్లలకు చెవులు కుట్టే సంస్కారం కర్ణవేధ. ఇది శ్రవణ శక్తిని, మేధస్సును పెంచే శాస్త్రీయ పద్ధతి.",
        "hi": "कर्णवेध संस्कार से एकाग्रता, श्रवण शक्ति एवं स्वास्थ्य लाभ होता है।"
      },
      "religious_significance": {
        "en": "Prepares the child to receive Vedic mantras and holy sounds through purified ears.",
        "te": "పవిత్రమైన వేద మంత్రాలను, భగవన్నామాన్ని చక్కగా ఆలకించడానికి చెవులను సంస్కరిస్తారు.",
        "hi": "पवित्र मंत्रों के श्रवण हेतु कानों की शुद्धि का संस्कार।"
      },
      "why_muhurtham": {
        "en": "Prevents infections and balances the nervous system according to Ayurveda.",
        "te": "ఆయుర్వేద పరంగా నాడీ వ్యవస్థను నియంత్రించడానికి సరైన లగ్నంలో చేస్తారు.",
        "hi": "नाड़ी शुद्धि एवं रोगों से बचाव हेतु शुभ लग्न अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Conducted in morning hours during Shukla Paksha. Favorable Nakshatras include Ashwini, Pushya, Hasta, and Revati.",
        "te": "శుక్ల పక్ష ఉదయ వేళలు, హస్త, పుష్యమి, రేవతి నక్షత్రాలు అనుకూలం.",
        "hi": "शुक्ल पक्ष के प्रातःकाल, हस्त व पुष्य नक्षत्र में करना उत्तम है।"
      },
      "required_details": {
        "en": "Child name, DOB, location, and date range.",
        "te": "పిల్లల పేరు, పుట్టిన తేదీ, ప్రదేశం, తేదీల పరిధి.",
        "hi": "नाम, जन्म तिथि, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "vidyarambham": {
    "slug": "vidyarambham",
    "legacy_slug": "vidyarambham",
    "icon": "📚",
    "category": "baby_family",
    "title": {
      "en": "Vidyarambham / Aksharabhyasam Muhurtham",
      "te": "అక్షరాభ్యాసం ముహూర్తం (Aksharabhyasam)",
      "hi": "विद्यारंभ संस्कार मुहूर्त (Aksharabhyasam)"
    },
    "short_desc": {
      "en": "Initiation of learning and writing first sacred alphabets auspicious timings.",
      "te": "సరస్వతీ దేవి సన్నిధిలో అక్షరాభ్యాసం, విద్యా ప్రారంభ శుభ ముహూర్తాలు.",
      "hi": "शिशु के प्रथम अक्षर लेखन एवं विद्यारंभ का शुभ मुहूर्त।"
    },
    "form_schema": "vidyarambham",
    "content": {
      "about": {
        "en": "Vidyarambham (Aksharabhyasam) is the initiation of children into the world of alphabets, literature, and learning by writing on grains of rice.",
        "te": "పిల్లలకు విద్యాభ్యాసాన్ని ప్రారంభించే పవిత్ర వేడుక అక్షరాభ్యాసం. బియ్యంలో ఓంకారాన్ని రాయించి జ్ఞానాన్ని ప్రసాదిస్తారు.",
        "hi": "अक्षराभ्यास शिशु के औपचारिक अध्ययन का शुभारंभ है। चावल पर ॐ लिखवाकर विद्या की शुरुआत की जाती है।"
      },
      "religious_significance": {
        "en": "Goddess Saraswati, Lord Ganesha, and Lord Hayagriva are worshipped for intellect (Buddhi) and wisdom.",
        "te": "సరస్వతీ దేవి, గణపతి, హయగ్రీవ స్వామి అనుగ్రహంతో విద్యా ప్రాప్తి కలుగుతుంది.",
        "hi": "मां सरस्वती एवं गणेश जी की कृपा से बालक को उत्तम विद्या और मेधा प्राप्त होती है।"
      },
      "why_muhurtham": {
        "en": "Instills focus, academic excellence, and speech fluency in the child's formative years.",
        "te": "చదువులో ఏకాగ్రత, ఉన్నత విజయాలు సాధించడానికి శుభ ముహూర్తం ఎంతో అవసరం.",
        "hi": "शिक्षा में एकाग्रता और सफलता हेतु शुभ मुहूर्त की आवश्यकता होती है।"
      },
      "selection_principles": {
        "en": "Vijayadashami, Vasant Panchami, and Shukla Paksha Tritiya/Panchami are prime. Wednesday and Thursday are favored.",
        "te": "విజయదశమి, వసంత పంచమి, బుధ మరియు గురువారాలు అత్యంత ప్రశస్తమైనవి.",
        "hi": "विजयादशमी, वसंत पंचमी तथा बुधवार व गुरुवार सर्वोत्तम हैं।"
      },
      "required_details": {
        "en": "Child name, DOB, preferred temple or home location, and date range.",
        "te": "పిల్లల పేరు, పుట్టిన తేదీ, దేవాలయం లేదా ఇల్లు, తేదీల పరిధి.",
        "hi": "नाम, जन्म तिथि, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "upanayanam": {
    "slug": "upanayanam",
    "legacy_slug": "upanayanam",
    "icon": "📿",
    "category": "baby_family",
    "title": {
      "en": "Upanayanam / Sacred Thread Muhurtham",
      "te": "ఉపనయనం ముహూర్తం (Upanayanam)",
      "hi": "उपनयन / यज्ञोपवीत संस्कार मुहूर्त (Upanayanam)"
    },
    "short_desc": {
      "en": "Sacred Janeu investiture and Gayatri initiation auspicious timings.",
      "te": "యజ్ఞోపవీత ధారణ, గాయత్రీ మంత్రోపదేశం కొరకు శ్రేష్ఠమైన శుభ ముహూర్తాలు.",
      "hi": "यज्ञोपवीत (जनेऊ) धारण एवं गायत्री दीक्षा का पावन मुहूर्त।"
    },
    "form_schema": "upanayanam",
    "content": {
      "about": {
        "en": "Upanayanam (Janeu or Munja) is the sacred rite of passage where the young initiate receives the sacred thread and is initiated into the Gayatri Mantra, becoming a Dvijatva (twice-born).",
        "te": "బ్రహ్మోపదేశం చేసి యజ్ఞోపవీతం ధరింపజేసే పరమ పవిత్ర సంస్కారం ఉపనయనం. దీనితో బాలుడు ద్విజుడై వేదాధ్యయనానికి అర్హుడవుతాడు.",
        "hi": "उपनयन संस्कार बालक को द्विजत्व प्रदान करता है। इसमें गुरु द्वारा गायत्री मंत्र की दीक्षा और यज्ञोपवीत प्रदान किया जाता है।"
      },
      "religious_significance": {
        "en": "Opens the spiritual eye to study the Vedas and invokes Surya as the supreme preceptor.",
        "te": "గాయత్రీ మాత అనుగ్రహంతో బుద్ధి ప్రచోదనం జరిగి సకల విజ్ఞానాలు సిద్ధిస్తాయి.",
        "hi": "गायत्री मंत्र के जाप से आत्मतेज और ज्ञान की वृद्धि होती है।"
      },
      "why_muhurtham": {
        "en": "Ensures the Brahmachari maintains strict spiritual discipline, intellect, and moral strength.",
        "te": "బ్రహ్మచర్యాన్ని నిష్ఠగా పాటించి, గొప్ప పండితుడిగా ఎదగడానికి నిర్దోష లగ్నం ముఖ్యం.",
        "hi": "बालक में सदाचार, विद्या और तप का संचार करने हेतु शुभ लग्न अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Conducted in Uttarayana. Guru and Shukra must be in direct motion. Shukla Paksha is preferred.",
        "te": "ఉత్తరాయణం, శుక్ల పక్షం, గురు-శుక్రుల అనుకూల స్థితి తప్పనిసరి.",
        "hi": "उत्तरायण, शुक्ल पक्ष एवं गुरु-शुक्र का उदित रहना आवश्यक है।"
      },
      "required_details": {
        "en": "Vatu (boy) name, DOB, birth time, father/Guru name, ceremony venue, and date range.",
        "te": "వటువు పేరు, పుట్టిన వివరాలు, తండ్రి పేరు, వేదిక, తేదీల పరిధి.",
        "hi": "बटु का नाम, जन्म विवरण, पिता का नाम, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "seemantham": {
    "slug": "seemantham",
    "legacy_slug": "seemantham",
    "icon": "🤰",
    "category": "baby_family",
    "title": {
      "en": "Seemantham / Godh Bharai Muhurtham",
      "te": "సీమంతం ముహూర్తం (Seemantham)",
      "hi": "सीमंतोन्नयन / गोद भराई मुहूर्त (Seemantham)"
    },
    "short_desc": {
      "en": "Auspicious timings for pregnancy blessing and mother-to-be honoring rituals.",
      "te": "గర్భిణీ స్త్రీకి సీమంతం, ఒడిబియ్యం నింపడానికి శుభ ముహూర్తాలు.",
      "hi": "गर्भवती माता के मंगल एवं गोद भराई संस्कार का शुभ मुहूर्त।"
    },
    "form_schema": "seemantham",
    "content": {
      "about": {
        "en": "Seemantham (Godh Bharai or Valaikappu) is performed during the 6th, 7th, or 8th month of pregnancy to bestow mental serenity upon the expectant mother and invite divine protection for the unborn baby.",
        "te": "గర్భం దాల్చిన 6, 7 లేదా 8వ నెలలో గర్భిణీ స్త్రీకి, కడుపులోని బిడ్డకు శుభం కలగాలని చేసే వేడుక సీమంతం.",
        "hi": "सीमंतोन्नयन संस्कार गर्भस्थ शिशु एवं माता की कुशलता हेतु गर्भावस्था के 6वें, 7वें या 8वें माह में किया जाता है।"
      },
      "religious_significance": {
        "en": "Invokes Goddess Sri Lakshmi and Raka (full moon deity) for peaceful gestation and safe delivery.",
        "te": "శ్రీలక్ష్మీదేవి అనుగ్రహంతో సుఖప్రసవం జరిగి తేజోవంతమైన బిడ్డ జన్మిస్తుంది.",
        "hi": "लक्ष्मी जी की कृपा से प्रसव सुगम और शिशु तेजस्वी होता है।"
      },
      "why_muhurtham": {
        "en": "Shields against pregnancy complications and infuses pure thoughts into the fetus.",
        "te": "మానసిక ప్రశాంతతను ఇచ్చి సుఖప్రసవానికి సహాయపడుతుంది.",
        "hi": "शिशु के संस्कार और माता के मानसिक स्वास्थ्य हेतु शुभ मुहूर्त देखा जाता है।"
      },
      "selection_principles": {
        "en": "Conducted in Shukla Paksha during the appropriate gestational month. Rohini, Mrigashira, Pushya, and Hasta are favored.",
        "te": "శుక్ల పక్షం, రోహిణి, మృగశిర, హస్త నక్షత్రాలు అనుకూలం.",
        "hi": "शुक्ल पक्ष एवं शुभ नक्षत्रों में संस्कार संपन्न किया जाता है।"
      },
      "required_details": {
        "en": "Expectant mother name, husband name, pregnancy month, location, and date range.",
        "te": "గర్భిణి పేరు, భర్త పేరు, గర్భ మాసం, వేదిక, తేదీల పరిధి.",
        "hi": "माता का नाम, पिता का नाम, गर्भ मास, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "pumsavana": {
    "slug": "pumsavana",
    "legacy_slug": "pumsavana",
    "icon": "🌱",
    "category": "baby_family",
    "title": {
      "en": "Pumsavana Muhurtham",
      "te": "పుంసవన ముహూర్తం (Pumsavana)",
      "hi": "पुंसवन संस्कार मुहूर्त (Pumsavana)"
    },
    "short_desc": {
      "en": "Auspicious timings for prenatal vitality and fetal wellness rites.",
      "te": "గర్భస్థ శిశువు సంపూర్ణ ఎదుగుదల, ఆరోగ్య రక్షణ కొరకు పుంసవన ముహూర్తం.",
      "hi": "गर्भस्थ शिशु के सर्वांगीण विकास एवं स्वास्थ्य हेतु पुंसवन मुहूर्त।"
    },
    "form_schema": "pumsavana",
    "content": {
      "about": {
        "en": "Pumsavana is the second Vedic samskara performed during the 2nd or 3rd month of pregnancy to ensure the physical strength and vigor of the fetus.",
        "te": "గర్భధారణ జరిగిన 2 లేదా 3వ మాసంలో శిశువు చక్కటి ఎదుగుదలకు చేసే సంస్కారం పుంసవనం.",
        "hi": "पुंसवन संस्कार गर्भ के दूसरे या तीसरे माह में गर्भस्थ शिशु के ओज और बल की वृद्धि हेतु किया जाता है।"
      },
      "religious_significance": {
        "en": "Herbal extracts are offered with Vedic mantras to protect the womb.",
        "te": "ఔషధ మూలికలతో కూడిన పూజలు గర్భాన్ని సురక్షితంగా ఉంచుతాయి.",
        "hi": "वैदिक मंत्रों और औषधियों से गर्भ की रक्षा की जाती है।"
      },
      "why_muhurtham": {
        "en": "Guards against early pregnancy vulnerability and promotes strong vitality.",
        "te": "గర్భస్రావాలు జరగకుండా బిడ్డ దృఢంగా ఎదగడానికి ముహూర్తం చూస్తారు.",
        "hi": "गर्भपात से रक्षा और शिशु की मजबूती हेतु शुभ मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Pushya, Shravana, and Hasta Nakshatras on Shukla Paksha are favored.",
        "te": "పుష్యమి, శ్రవణం, హస్త నక్షత్రాలు అనుకూలం.",
        "hi": "पुष्य एवं श्रवण नक्षत्र में यह संस्कार उत्तम है।"
      },
      "required_details": {
        "en": "Mother name, gestational month, location, and date range.",
        "te": "తల్లి పేరు, గర్భ మాసం, ప్రదేశం, తేదీల పరిధి.",
        "hi": "माता का नाम, गर्भ मास एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "jatakarma": {
    "slug": "jatakarma",
    "legacy_slug": "jatakarma",
    "icon": "🌟",
    "category": "baby_family",
    "title": {
      "en": "Jatakarma Muhurtham",
      "te": "జాతకర్మ ముహూర్తం (Jatakarma)",
      "hi": "जातकर्म संस्कार मुहूर्त (Birth Rite)"
    },
    "short_desc": {
      "en": "Vedic natal rites performed immediately upon the arrival of the newborn.",
      "te": "శిశువు జన్మించిన వెంటనే జరిపే పవిత్ర జాతకర్మ శుభ సమయాలు.",
      "hi": "शिशु के जन्म के उपरांत किए जाने वाले जातकर्म संस्कार का शुभ मुहूर्त।"
    },
    "form_schema": "jatakarma",
    "content": {
      "about": {
        "en": "Jatakarma is the birth samskara performed upon birth where the father feeds drops of honey and ghee with a gold ring while chanting prayers for intellect (Medha Janana).",
        "te": "శిశువు జన్మించినప్పుడు తండ్రి తేనె, నెయ్యిని బంగారు శలాకతో నాలుకపై ఉంచి మేధాశక్తిని ప్రార్థించే సంస్కారం.",
        "hi": "शिशु के जन्म पर पिता द्वारा स्वर्ण शलाका से मधु-घृत चटाकर मेधा जनन की प्रार्थना की जाती है।"
      },
      "religious_significance": {
        "en": "Ignites the child's intellect and wards off negative spirits at the threshold of life.",
        "te": "బాలారిష్టాలు తొలగి ఆయువు, ఆరోగ్యం నిలవడానికి ఈ కర్మ చేస్తారు.",
        "hi": "शिशु को दीर्घायु एवं कुशाग्र बुद्धि प्रदान करने का प्रथम संस्कार।"
      },
      "why_muhurtham": {
        "en": "Ensures the spiritual transition from the womb into the earthly realm is divinely sanctified.",
        "te": "జన్మ సమయంలోని గ్రహదోషాలను తొలగించడానికి ఈ పూజ ఉపకరిస్తుంది.",
        "hi": "जन्म दोषों के निवारण हेतु जातकर्म आवश्यक है।"
      },
      "selection_principles": {
        "en": "Performed immediately before cutting the umbilical cord or on appropriate post-natal shuddhi days.",
        "te": "జననం తర్వాత శాస్త్రోక్త దినాలలో నిర్వహిస్తారు.",
        "hi": "जन्म के तत्काल बाद अथवा शुद्धि काल में संपन्न किया जाता है।"
      },
      "required_details": {
        "en": "Newborn birth details, father name, hospital/home location, and date range.",
        "te": "శిశువు జనన వివరాలు, తండ్రి పేరు, స్థలం, తేదీల పరిధి.",
        "hi": "शिशु का जन्म विवरण, पिता का नाम एवं स्थल।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "ayushya-homam": {
    "slug": "ayushya-homam",
    "legacy_slug": "ayushya-homam",
    "icon": "🔥",
    "category": "baby_family",
    "title": {
      "en": "Ayushya Homam Muhurtham",
      "te": "ఆయుష్య హోమం ముహూర్తం (Ayushya Homam)",
      "hi": "आयुष्य होम मुहूर्त (Longevity Ritual)"
    },
    "short_desc": {
      "en": "Auspicious timings for life-extending longevity homams on birthdays and milestones.",
      "te": "ఆయురారోగ్యాల వృద్ధి కొరకు జరిపే ఆయుష్య హోమ శుభ ముహూర్తాలు.",
      "hi": "दीर्घायु एवं स्वास्थ्य वृद्धि हेतु आयुष्य होम का शुभ मुहूर्त।"
    },
    "form_schema": "ayushya_homam",
    "content": {
      "about": {
        "en": "Ayushya Homam is a sacred Vedic fire ritual performed on the first birthday of a child, or during major life milestones, invoking Lord Ayur Devata, Markandeya, and the Chiranjeevis for longevity.",
        "te": "మొదటి పుట్టినరోజున లేదా జీవిత ముఖ్య ఘట్టాలలో ఆయురారోగ్యాలు, వ్యాధి నివారణ కొరకు ఆయుష్య హోమం చేస్తారు.",
        "hi": "प्रथम जन्मदिवस अथवा जीवन के प्रमुख पड़ावों पर दीर्घायु हेतु आयुष्य होम किया जाता है।"
      },
      "religious_significance": {
        "en": "Propitiates Agni, Yama, and the seven immortals (Saptarishis) to grant health and protection.",
        "te": "మార్కండేయ, చిరంజీవుల ఆశీస్సులతో అపమృత్యు భయాలు తొలగిపోతాయి.",
        "hi": "मार्कंडेय ऋषि एवं सप्त चिरंजीवियों की कृपा से अकाल मृत्यु का भय समाप्त होता है।"
      },
      "why_muhurtham": {
        "en": "Aligns the ritual with the native's Janma Nakshatra to generate maximum protective vitality.",
        "te": "జన్మ నక్షత్రం రోజున సరైన ముహూర్తంలో చేస్తే హోమ ఫలితం సంపూర్ణంగా దక్కుతుంది.",
        "hi": "जन्म नक्षत्र पर शुभ मुहूर्त में होम करने से अक्षय आरोग्य प्राप्त होता है।"
      },
      "selection_principles": {
        "en": "The native's Janma Nakshatra or favorable Tarabala days are prioritized.",
        "te": "జన్మ నక్షత్రం లేదా అనుకూల తారాబలం గల రోజులు శ్రేష్ఠం.",
        "hi": "जन्म नक्षत्र एवं अनुकूल ताराबल के दिन यह होम करना चाहिए।"
      },
      "required_details": {
        "en": "Individual/child name, birth date & star, location, and date range.",
        "te": "వ్యక్తి పేరు, పుట్టిన నక్షత్రం, ప్రదేశం, తేదీల పరిధి.",
        "hi": "नाम, जन्म नक्षत्र, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "family-milestones": {
    "slug": "family-milestones",
    "legacy_slug": "family-milestones",
    "icon": "👑",
    "category": "baby_family",
    "title": {
      "en": "Family Milestone Ceremonies Muhurtham",
      "te": "షష్టిపూర్తి / శతాభిషేకం ముహూర్తం (Milestone Ceremonies)",
      "hi": "षष्टिपूर्ति / शताभिषेक मुहूर्त (Milestone Ceremonies)"
    },
    "short_desc": {
      "en": "Auspicious timing for Shashti Poorthi (60th), Bhima Ratha (70th), and Satabhishekam (80th).",
      "te": "60వ పుట్టినరోజు షష్టిపూర్తి, భీమరథ, శతాభిషేక శుభ ముహూర్తాలు.",
      "hi": "षष्टिपूर्ति (60 वर्ष), भीमरथ (70 वर्ष) एवं शताभिषेक (80 वर्ष) का शुभ मुहूर्त।"
    },
    "form_schema": "family_milestones",
    "content": {
      "about": {
        "en": "Vedic family milestone ceremonies celebrate longevity and spiritual fruition: Shashti Abda Poorthi (60th year), Bhima Ratha Shanti (70th year), and Satabhishekam (completion of seeing 1000 full moons at ~80 years).",
        "te": "జీవితంలో 60 సంవత్సరాలు నిండినప్పుడు షష్టిపూర్తి, 70కి భీమరథ శాంతి, 1000 పున్నములు చూసినందుకు శతాభిషేకం శాస్త్రోక్తంగా నిర్వహిస్తారు.",
        "hi": "षष्टिपूर्ति (60 वर्ष), भीमरथ शांति (70 वर्ष) एवं सहस्र चंद्र दर्शन (शताभिषेक 80 वर्ष) जीवन के परम पावन उत्सव हैं।"
      },
      "religious_significance": {
        "en": "Re-enacts wedding vows (Ugra Ratha Shanti) and washes away accumulated karmic burdens through holy Kalasha abhishekam.",
        "te": "దంపతులు మళ్లీ కళ్యాణం జరుపుకుని కలశోదకాలతో స్నానం చేసి ఆశీర్వాదం పొందుతారు.",
        "hi": "कलश स्नान और पुनः पाणिग्रहण से समस्त ग्रह बाधाएं शांत होती हैं।"
      },
      "why_muhurtham": {
        "en": "Rejuvenates longevity, bestows peace of mind in vanaprastha, and blesses future generations.",
        "te": "వంశాభివృద్ధి, ఆయురారోగ్యాలు, మనశ్శాంతి కలగడానికి నిర్దోష లగ్నంలో చేస్తారు.",
        "hi": "वंश वृद्धि और मोक्ष मार्ग प्रशस्त करने हेतु शुभ मुहूर्त देखा जाता है।"
      },
      "selection_principles": {
        "en": "The birth month and Janma Nakshatra of the elder are calculated with Brihaspati and Surya horas.",
        "te": "జన్మ మాసం, జన్మ నక్షత్రం, గురు-రవి అనుకూలతలు ప్రధానం.",
        "hi": "जन्म मास एवं जन्म नक्षत्र के आधार पर शुभ लग्न चुना जाता है।"
      },
      "required_details": {
        "en": "Elder couple names, milestone type (60th, 70th, 80th), birth star, location, and date range.",
        "te": "దంపతుల పేర్లు, వేడుక రకం, పుట్టిన నక్షత్రం, వేదిక, తేదీల పరిధి.",
        "hi": "दंपति का नाम, संस्कार का प्रकार, जन्म नक्षत्र, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "vehicle-purchase": {
    "slug": "vehicle-purchase",
    "legacy_slug": "vehicle-purchase",
    "icon": "🚗",
    "category": "vehicles_purchases",
    "title": {
      "en": "Vehicle Purchase Muhurtham",
      "te": "వాహన కొనుగోలు ముహూర్తం (Vehicle Purchase)",
      "hi": "वाहन क्रय मुहूर्त (Vehicle Purchase)"
    },
    "short_desc": {
      "en": "Auspicious timing for buying cars, motorcycles, commercial vehicles, and EVs.",
      "te": "కార్లు, బైకులు, వాణిజ్య వాహనాలు కొనుగోలు చేయడానికి శుభ ముహూర్తాలు.",
      "hi": "कार, बाइक, व्यावसायिक वाहन खरीदने हेतु शास्त्रोक्त शुभ मुहूर्त।"
    },
    "form_schema": "vehicle_purchase",
    "content": {
      "about": {
        "en": "Purchasing a vehicle represents both mobility and security. Choosing an auspicious cosmic moment ensures travel safety, longevity of the machine, and freedom from accidents.",
        "te": "నూతన వాహనాన్ని కొనుగోలు చేయడం ప్రయాణ సౌలభ్యాన్ని, గౌరవాన్ని ఇస్తుంది. సరైన సమయంలో కొంటే వాహనం సురక్షితంగా, ప్రమాదాలు లేకుండా నడుస్తుంది.",
        "hi": "वाहन क्रय हेतु शुभ मुहूर्त का चयन यात्रा की सुरक्षा और दुर्घटनाओं से मुक्ति सुनिश्चित करता है।"
      },
      "religious_significance": {
        "en": "Invokes Lord Ashwini Kumaras and Lord Hanuman for invincibility and protection on all roads.",
        "te": "అశ్వినీ దేవతలు మరియు హనుమంతుని అనుగ్రహం వల్ల మార్గమధ్యంలో ఎటువంటి అవాంతరాలు రావు.",
        "hi": "अश्विनी कुमार एवं हनुमान जी की कृपा से मार्ग में सदैव सुरक्षा रहती है।"
      },
      "why_muhurtham": {
        "en": "Prevents vehicular breakdowns, sudden repair expenses, and insurance disputes.",
        "te": "వాహనానికి తరచుగా రిపేర్లు రాకుండా, ధన నష్టం కలగకుండా కాపాడుతుంది.",
        "hi": "बार-बार खराब होने और अनावश्यक मरम्मत खर्च से बचाव होता है।"
      },
      "selection_principles": {
        "en": "Chara (movable) Lagna is favored. Ashwini, Rohini, Punarvasu, Pushya, Hasta, Swati, Shravana, and Revati are favored. Tuesday is strictly avoided.",
        "te": "చర లగ్నాలు, రోహిణి, హస్త, స్వాతి, శ్రవణం నక్షత్రాలు మరియు సోమ, బుధ, గురు, శుక్రవారాలు అనుకూలం. మంగళవారం నిషిద్ధం.",
        "hi": "चर लग्न, शुभ नक्षत्र तथा सोमवार, बुधवार, गुरुवार एवं शुक्रवार वाहन हेतु उत्तम हैं।"
      },
      "required_details": {
        "en": "Purchaser name, vehicle type (car, bike, commercial), showroom location, date range, and preferred hours.",
        "te": "కొనుగోలుదారు పేరు, వాహనం రకం, షోరూమ్ ప్రదేశం, తేదీల పరిధి, ప్రాధాన్య సమయం.",
        "hi": "क्रेता का नाम, वाहन का प्रकार, स्थल, दिनांक एवं समय।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        7,
        8,
        12,
        13,
        15,
        17,
        21,
        22,
        23,
        24,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        10,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "vehicle-delivery": {
    "slug": "vehicle-delivery",
    "legacy_slug": "vehicle-delivery",
    "icon": "🔑",
    "category": "vehicles_purchases",
    "title": {
      "en": "Vehicle Delivery Muhurtham",
      "te": "వాహన డెలివరీ ముహూర్తం (Vehicle Delivery)",
      "hi": "वाहन डिलीवरी मुहूर्त (Vehicle Delivery)"
    },
    "short_desc": {
      "en": "Auspicious timing for receiving keys and driving your new vehicle off the showroom.",
      "te": "షోరూమ్ నుండి తాళాలు తీసుకుని వాహనాన్ని బయటకు తీసే శుభ ముహూర్తాలు.",
      "hi": "शोरूम से चाबी लेने एवं वाहन को प्रथम बार चलाने का शुभ मुहूर्त।"
    },
    "form_schema": "vehicle_delivery",
    "content": {
      "about": {
        "en": "The exact moment you take possession of the vehicle keys and turn on the ignition sets the primary motion hora for the vehicle's operational life.",
        "te": "షోరూమ్ వద్ద తాళాలు అందుకుని ఇంజిన్ స్టార్ట్ చేసే ఖచ్చితమైన సమయం వాహన ఆయుష్షుకు ప్రధానం.",
        "hi": "वाहन की चाबी प्राप्त कर प्रथम बार स्टार्ट करने का क्षण अत्यंत महत्वपूर्ण होता है।"
      },
      "religious_significance": {
        "en": "Breaking the ceremonial coconut (Kobbari Kaya) and offering lemons wards off negative eyes and Drishti doshas.",
        "te": "కొబ్బరికాయ కొట్టి, నిమ్మకాయల పూజ చేయడం ద్వారా సమస్త దృష్టి దోషాలు తొలగిపోతాయి.",
        "hi": "नींबू-नारियल से वाहन की नजर उतारकर शुभ समय में प्रस्थान किया जाता है।"
      },
      "why_muhurtham": {
        "en": "Ensures the vehicle's maiden voyage back home is smooth, blissful, and safe.",
        "te": "తొలి ప్రయాణం క్షేమంగా, ఆనందంగా సాగడానికి రాహుకాలం లేని సమయం ఎంచుకుంటారు.",
        "hi": "प्रथम यात्रा सुरक्षित और मंगलमय हो, इसलिए मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Avoid Rahu Kalam, Yamagandam, and Dur Muhurtham. Concurs with daytime Shubh/Amrit Horas.",
        "te": "రాహుకాలం, యమగండం లేని శుభ హోరాలలో వాహనాన్ని బయటకు తీయాలి.",
        "hi": "राहुकाल एवं यमगंड से मुक्त अमृत होरा में डिलीवरी लेनी चाहिए।"
      },
      "required_details": {
        "en": "Owner name, vehicle model/number, showroom location, and date range.",
        "te": "యజమాని పేరు, మోడల్, షోరూమ్ స్థలం, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, मॉडल, शोरूम का पता एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "vehicle-puja": {
    "slug": "vehicle-puja",
    "legacy_slug": "vehicle-puja",
    "icon": "🪔",
    "category": "vehicles_purchases",
    "title": {
      "en": "Vehicle Puja Muhurtham",
      "te": "వాహన పూజ ముహూర్తం (Vahana Puja)",
      "hi": "वाहन पूजा मुहूर्त (Vahana Puja)"
    },
    "short_desc": {
      "en": "Auspicious timing for Ayudha/Vahana Puja at temples or at home.",
      "te": "ఆలయాల వద్ద లేదా ఇంట్లో వాహనానికి శాస్త్రోక్త పూజ చేయించే శుభ ముహూర్తాలు.",
      "hi": "मंदिर अथवा घर पर वाहन की विधिवत पूजा कराने का शुभ मुहूर्त।"
    },
    "form_schema": "vehicle_puja",
    "content": {
      "about": {
        "en": "Vahana Puja consecrates the vehicle with vermilion (Sindoor), swastika, sacred mantras, and camphor arati to invite divine stewardship over every journey.",
        "te": "వాహనానికి స్వస్తిక్ వేసి, హారతి ఇచ్చి దేవతల రక్షణ కోరుతూ చేసే పూజ వాహన పూజ.",
        "hi": "वाहन पर स्वास्तिक बनाकर आरती एवं मंत्रोच्चार से की जाने वाली पूजा।"
      },
      "religious_significance": {
        "en": "Invokes Lord Ganesha to remove obstacles and Goddess Durga for invulnerable armor.",
        "te": "గణపతి, దుర్గాదేవిల అనుగ్రహంతో ప్రయాణాలలో రక్షణ లభిస్తుంది.",
        "hi": "गणेश जी एवं दुर्गा माता की कृपा से हर संकट टल जाता है।"
      },
      "why_muhurtham": {
        "en": "Activates divine energy on the vehicle before long-distance journeys are undertaken.",
        "te": "సుదూర ప్రయాణాలు చేసే ముందు వాహనానికి దైవిక శక్తిని చేకూరుస్తుంది.",
        "hi": "लंबी यात्राओं से पूर्व वाहन में सुरक्षा कवच स्थापित होता है।"
      },
      "selection_principles": {
        "en": "Morning and evening sandhya times free of Gulika and Rahu Kalam are preferred.",
        "te": "ఉదయం మరియు సాయంత్రం వేళలు అనుకూలం.",
        "hi": "प्रातःकाल अथवा गोधूलि वेला में पूजा करना श्रेयस्कर है।"
      },
      "required_details": {
        "en": "Owner name, vehicle type, temple/home location, and date range.",
        "te": "యజమాని పేరు, వాహనం రకం, ఆలయం/ఇంటి ప్రదేశం, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, स्थल एवं दिनांक সীমা।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "vehicle-registration": {
    "slug": "vehicle-registration",
    "legacy_slug": "vehicle-registration",
    "icon": "📑",
    "category": "vehicles_purchases",
    "title": {
      "en": "Vehicle Registration Muhurtham",
      "te": "వాహన రిజిస్ట్రేషన్ ముహూర్తం (Vehicle Registration)",
      "hi": "वाहन पंजीयन मुहूर्त (Vehicle Registration)"
    },
    "short_desc": {
      "en": "Auspicious timing for RTO authority registration and number plate allotment.",
      "te": "ఆర్టీఓ కార్యాలయంలో వాహన రిజిస్ట్రేషన్ దరఖాస్తు చేసుకోవడానికి ముహూర్తాలు.",
      "hi": "परिवहन कार्यालय (RTO) में पंजीयन एवं नंबर प्लेट आवंटन मुहूर्त।"
    },
    "form_schema": "vehicle_registration",
    "content": {
      "about": {
        "en": "Filing vehicle registration papers at the RTO during an auspicious astrological window ensures hassle-free documentation and official compliance.",
        "te": "రవాణా శాఖ కార్యాలయంలో రిజిస్ట్రేషన్ పత్రాలు సమర్పించడానికి శుభ ముహూర్తం చూస్తారు.",
        "hi": "RTO में पंजीयन प्रपत्र जमा करने का शुभ समय।"
      },
      "religious_significance": {
        "en": "Mercury (Budha) rules documentation and trade regulations, ensuring legal peace.",
        "te": "బుధ గ్రహ అనుగ్రహం వల్ల చట్టపరమైన సమస్యలు రావు.",
        "hi": "बुध ग्रह की कृपा से शासकीय कार्य निर्विघ्न संपन्न होते हैं।"
      },
      "why_muhurtham": {
        "en": "Prevents bureaucratic delays, challan disputes, and licensing obstacles.",
        "te": "రిజిస్ట్రేషన్ పనుల్లో జాప్యం జరగకుండా సత్వరం పూర్తి కావడానికి ఉపకరిస్తుంది.",
        "hi": "दस्तावेजी अड़चनों से बचाव हेतु मुहूर्त विचार किया जाता है।"
      },
      "selection_principles": {
        "en": "Weekdays matching government hours with favorable Mercury/Jupiter horas.",
        "te": "కార్యాలయ వేళలలో బుధ లేదా గురు హోరలు అనుకూలం.",
        "hi": "कार्यालयी समय के अनुसार शुभ होरा ग्राह्य है।"
      },
      "required_details": {
        "en": "Owner name, RTO office location, and date range.",
        "te": "యజమాని పేరు, ఆర్టీఓ ఆఫీస్ స్థలం, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, RTO कार्यालय स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "gold-purchase": {
    "slug": "gold-purchase",
    "legacy_slug": "gold-purchase",
    "icon": "🪙",
    "category": "vehicles_purchases",
    "title": {
      "en": "Gold & Jewellery Purchase Muhurtham",
      "te": "బంగారం కొనుగోలు ముహూర్తం (Gold & Jewellery)",
      "hi": "स्वर्ण एवं आभूषण क्रय मुहूर्त (Gold Purchase)"
    },
    "short_desc": {
      "en": "Auspicious timings for buying pure gold, diamond jewellery, and silver bullion.",
      "te": "బంగారం, వెండి, ఆభరణాలు కొనుగోలు చేయడానికి శ్రేష్ఠమైన శుభ ముహూర్తాలు.",
      "hi": "सोना, चांदी एवं रत्न-आभूषण खरीदने का अत्यंत शुभ मुहूर्त।"
    },
    "form_schema": "gold_purchase",
    "content": {
      "about": {
        "en": "Buying gold and precious ornaments on auspicious days (such as Pushya Nakshatra and Akshaya Tritiya) multiplies wealth and ushers eternal prosperity into the household.",
        "te": "పుష్యమి నక్షత్రం, అక్షయ తృతీయ వంటి శుభ దినాలలో బంగారం కొంటే సంపద ఇనుమడిస్తుందని నమ్మకం.",
        "hi": "पुष्य नक्षत्र एवं शुभ योगों में सोना खरीदने से घर में लक्ष्मी का चिर वास होता है।"
      },
      "religious_significance": {
        "en": "Gold represents Suvarna, the physical manifestation of Goddess Lakshmi and Agni Deva.",
        "te": "బంగారం సాక్షాత్తు లక్ష్మీదేవి స్వరూపం; దీనిని కొనడం ధనవృద్ధికరమైనది.",
        "hi": "स्वर्ण मां महालक्ष्मी का प्रत्यक्ष स्वरूप माना गया है।"
      },
      "why_muhurtham": {
        "en": "Ensures the bought ornaments remain permanently in the family without distress pawnings.",
        "te": "కొన్న బంగారం అమ్ముకోనవసరం లేకుండా తరతరాలు వృద్ధి చెందడానికి ముహూర్తం ముఖ్యం.",
        "hi": "स्वर्ण संपत्ति सदैव सुरक्षित और बढ़ती रहे, इसलिए मुहूर्त देखा जाता है।"
      },
      "selection_principles": {
        "en": "Pushya (Guru Pushya, Ravi Pushya), Swati, Hasta, Rohini, and Dhanishta are supreme. Friday and Thursday are favored.",
        "te": "పుష్యమి, రోహిణి, హస్త, ధనిష్ఠ నక్షత్రాలు మరియు గురు, శుక్రవారాలు ఎంతో శ్రేష్ఠం.",
        "hi": "पुष्य, रोहिणी, हस्त, धनिष्ठा नक्षत्र एवं गुरुवार-शुक्रवार सर्वोत्तम हैं।"
      },
      "required_details": {
        "en": "Buyer name, purchase occasion, city, and date range.",
        "te": "కొనుగోలుదారు పేరు, సందర్భం, నగరం, తేదీల పరిధి.",
        "hi": "क्रेता का नाम, अवसर, नगर एवं दिनांक সীমা।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "electronics-purchase": {
    "slug": "electronics-purchase",
    "legacy_slug": "electronics-purchase",
    "icon": "📱",
    "category": "vehicles_purchases",
    "title": {
      "en": "Electronics & Machinery Purchase Muhurtham",
      "te": "ఎలక్ట్రానిక్స్ & యంత్రాల కొనుగోలు ముహూర్తం (Electronics)",
      "hi": "इलेक्ट्रॉनिक्स एवं मशीनरी क्रय मुहूर्त (Electronics)"
    },
    "short_desc": {
      "en": "Auspicious timings for purchasing laptops, appliances, machinery, and servers.",
      "te": "ల్యాప్‌టాప్‌లు, గృహోపకరణాలు, పారిశ్రామిక యంత్రాలు కొనడానికి ముహూర్తాలు.",
      "hi": "कंप्यूटर, घरेलू उपकरण एवं भारी मशीनरी खरीदने का शुभ मुहूर्त।"
    },
    "form_schema": "electronics_purchase",
    "content": {
      "about": {
        "en": "Purchasing high-value electronics, computers, and heavy machinery under favorable planetary rays protects against technical defects and premature breakdowns.",
        "te": "ఖరీదైన ఎలక్ట్రానిక్ వస్తువులు, కంప్యూటర్లు కొనేటప్పుడు యంత్ర దోషాలు రాకుండా ముహూర్తం చూస్తారు.",
        "hi": "तकनीकी खराबी से बचाव और उपकरणों की दीर्घायु हेतु शुभ काल में क्रय करें।"
      },
      "religious_significance": {
        "en": "Invokes Lord Vishwakarma and Mercury (deity of technology and computation).",
        "te": "విశ్వకర్మ మరియు బుధ గ్రహ అనుగ్రహం సాంకేతిక పరికరాలకు రక్షణ కల్పిస్తుంది.",
        "hi": "विश्वकर्मा देव एवं बुध की कृपा से उपकरण बिना रुकावट कार्य करते हैं।"
      },
      "why_muhurtham": {
        "en": "Protects against sudden voltage burnouts, software crashes, and costly repairs.",
        "te": "వస్తువులు పాడవకుండా ఎక్కువ కాలం మన్నడానికి సరైన సమయంలో కొంటారు.",
        "hi": "अनावश्यक मरम्मत खर्च और नुकसान से रक्षा होती है।"
      },
      "selection_principles": {
        "en": "Wednesday and Friday are favored. Favorable nakshatras include Chitra, Hasta, and Anuradha.",
        "te": "బుధ, శుక్రవారాలు మరియు హస్త, చిత్ర నక్షత్రాలు అనుకూలం.",
        "hi": "बुधवार एवं शुक्रवार को चित्रा, हस्त नक्षत्र में क्रय उत्तम है।"
      },
      "required_details": {
        "en": "Buyer name, device category, location, and date range.",
        "te": "కొనుగోలుదారు పేరు, వస్తువు రకం, స్థలం, తేదీల పరిధి.",
        "hi": "क्रेता का नाम, उपकरण का प्रकार, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "business-opening": {
    "slug": "business-opening",
    "legacy_slug": "business-opening",
    "icon": "💼",
    "category": "business_career",
    "title": {
      "en": "Business Opening Muhurtham",
      "te": "నూతన వ్యాపార ప్రారంభ ముహూర్తం (Business Opening)",
      "hi": "व्यापार शुभारंभ मुहूर्त (Business Opening)"
    },
    "short_desc": {
      "en": "Auspicious timing for grand openings of companies, commercial ventures, and firms.",
      "te": "కొత్త వ్యాపార సంస్థలు, కంపెనీలు ప్రారంభించడానికి శ్రేష్ఠమైన శుభ ముహూర్తాలు.",
      "hi": "नई कंपनी, प्रतिष्ठान एवं वाणिज्यिक उपक्रम के उद्घाटन का शुभ मुहूर्त।"
    },
    "form_schema": "business_opening",
    "content": {
      "about": {
        "en": "Inaugurating a commercial enterprise under an auspicious Muhurtham sets the enterprise on an enduring trajectory of profitability, customer trust, and steady expansion.",
        "te": "నూతన వాణిజ్య సంస్థను శుభ ముహూర్తంలో ప్రారంభిస్తే లాభాలు సమకూరి, సంస్థ దినదిన ప్రవర్ధమానమవుతుంది.",
        "hi": "शुभ मुहूर्त में व्यापार प्रारंभ करने से निरंतर लाभ, ग्राहकों का विश्वास और विस्तार होता है।"
      },
      "religious_significance": {
        "en": "Invokes Lord Ganesha (Vighneshwara), Sri Mahalakshmi, and Kubera to safeguard business capital.",
        "te": "గణపతి, మహాలక్ష్మి, కుబేరుల ఆరాధనతో వ్యాపారంలో నష్టాలు రాకుండా లాభాలు చేకూరుతాయి.",
        "hi": "गणेश जी, महालक्ष्मी एवं कुबेर देव की आराधना से व्यापार में स्थिरता आती है।"
      },
      "why_muhurtham": {
        "en": "Shields against cash-flow crises, partner disputes, and premature venture collapse.",
        "te": "వ్యాపారంలో భాగస్వామ్య గొడవలు, ఆర్థిక ఇబ్బందులు రాకుండా కాపాడుతుంది.",
        "hi": "आर्थिक संकट और विवादों से रक्षा हेतु शुभ लग्न आवश्यक है।"
      },
      "selection_principles": {
        "en": "Sthira Lagna is supreme. Favorable nakshatras include Ashwini, Rohini, Pushya, Uttara Phalguni, Hasta, Anuradha, and Revati. Wednesday and Thursday are favored.",
        "te": "స్థిర లగ్నాలు, రోహిణి, పుష్యమి, హస్త నక్షత్రాలు మరియు బుధ, గురువారాలు ఎంతో శ్రేష్ఠం.",
        "hi": "स्थिर लग्न, पुष्य, हस्त, अनुराधा नक्षत्र तथा बुधवार-गुरुवार सर्वोत्तम हैं।"
      },
      "required_details": {
        "en": "Owner name, business name, industry type, commercial address, and date range.",
        "te": "యజమాని పేరు, వ్యాపార నామం, రంగం, చిరునామా, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, व्यापार का नाम, उद्योग, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        8,
        12,
        13,
        15,
        17,
        21,
        22,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        18,
        24
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "shop-opening": {
    "slug": "shop-opening",
    "legacy_slug": "shop-opening",
    "icon": "🏬",
    "category": "business_career",
    "title": {
      "en": "Shop Opening Muhurtham",
      "te": "దుకాణ ప్రారంభ ముహూర్తం (Shop Opening)",
      "hi": "दुकान उद्घाटन मुहूर्त (Shop Opening)"
    },
    "short_desc": {
      "en": "Auspicious timings for opening retail stores, boutiques, and commercial outlets.",
      "te": "నూతన దుకాణాలు, షోరూమ్‌లు, రిటైల్ షాపులు ప్రారంభించడానికి ముహూర్తాలు.",
      "hi": "दुकान, शोरूम एवं खुदरा व्यापार के शुभारंभ का शुभ मुहूर्त।"
    },
    "form_schema": "shop_opening",
    "content": {
      "about": {
        "en": "Opening the shutters and conducting the first commercial sale in an auspicious Muhurtham invites steady footfall, customer loyalty, and brisk trade.",
        "te": "దుకాణం తెరిచి తొలి అమ్మకం జరిపే సమయం శాస్త్రోక్తంగా ఉంటే గిరాకీ బాగుంటుంది.",
        "hi": "दुकान का शटर उठाकर प्रथम बिक्री शुभ काल में करने से निरंतर ग्राहकी रहती है।"
      },
      "religious_significance": {
        "en": "Lighting the sacred brass lamp (Kuthu Vilakku) welcomes Goddess Lakshmi into the cash register (Galla Pette).",
        "te": "గల్లా పెట్టె వద్ద దీపారాధన చేసి లక్ష్మీదేవిని ఆహ్వానిస్తారు.",
        "hi": "गल्ले पर लक्ष्मी पूजन कर व्यापार में बरकत की प्रार्थना की जाती है।"
      },
      "why_muhurtham": {
        "en": "Prevents dead stock inventory, unpaid credit debts, and low sales.",
        "te": "సరుకు నిలిచిపోకుండా వేగంగా అమ్ముడై ధనలాభం కలగడానికి ముహూర్తం ముఖ్యం.",
        "hi": "उधारी फंसने और मंदे व्यापार से बचाव हेतु शुभ समय अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Shukla Paksha morning horas are prioritized, avoiding Rahu Kalam.",
        "te": "శుక్ల పక్ష ఉదయ వేళలు, రాహుకాలం లేని సమయం ఎంచుకుంటారు.",
        "hi": "प्रातःकाल के शुद्ध समय में उद्घाटन प्रशस्त है।"
      },
      "required_details": {
        "en": "Shopkeeper name, shop name, retail trade type, location, and date range.",
        "te": "వ్యాపారి పేరు, షాపు పేరు, వర్తక రకం, ప్రాంతం, తేదీల పరిధి.",
        "hi": "दुकानदार का नाम, दुकान का नाम, श्रेणी, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "office-inauguration": {
    "slug": "office-inauguration",
    "legacy_slug": "office-inauguration",
    "icon": "🏢",
    "category": "business_career",
    "title": {
      "en": "Office Inauguration Muhurtham",
      "te": "కార్యాలయ ప్రారంభ ముహూర్తం (Office Inauguration)",
      "hi": "कार्यालय उद्घाटन मुहूर्त (Office Inauguration)"
    },
    "short_desc": {
      "en": "Auspicious timing for inaugurating corporate offices, studios, and consultancy workspaces.",
      "te": "కార్పొరేట్ ఆఫీసులు, కన్సల్టెన్సీలు ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "कॉर्पोरेट कार्यालय, शाखा एवं कार्यस्थल के उद्घाटन का शुभ मुहूर्त।"
    },
    "form_schema": "office_inauguration",
    "content": {
      "about": {
        "en": "Inaugurating corporate offices and workspace branches during an auspicious hour ensures productive staff, positive work culture, and prestigious clients.",
        "te": "కార్యాలయాన్ని శుభ సమయంలో ప్రారంభిస్తే ఉద్యోగులు ఉత్సాహంగా పనిచేసి మంచి ప్రాజెక్టులు లభిస్తాయి.",
        "hi": "कार्यालय के उद्घाटन से कार्य संस्कृति सुदृढ़ और व्यावसायिक सफलता प्राप्त होती है।"
      },
      "religious_significance": {
        "en": "Ganapati Homa and Vastu Puja harmonize the professional atmosphere.",
        "te": "గణపతి హోమం, వాస్తు పూజలతో ఆఫీసు వాతావరణం సానుకూలంగా మారుతుంది.",
        "hi": "गणपति पूजन से कार्यक्षेत्र के समस्त विघ्न दूर होते हैं।"
      },
      "why_muhurtham": {
        "en": "Shields against employee attrition, client disputes, and rent escalations.",
        "te": "సిబ్బంది కొరత, క్లయింట్ సమస్యలు రాకుండా ఆఫీసు స్థిరంగా నడుస్తుంది.",
        "hi": "कार्यालय में स्थिरता और प्रगति हेतु शुभ लग्न देखा जाता है।"
      },
      "selection_principles": {
        "en": "Fixed and dual signs (Gemini, Virgo, Sagittarius, Taurus, Leo) are favored.",
        "te": "స్థిర మరియు ద్విస్వభావ లగ్నాలు శ్రేష్ఠమైనవి.",
        "hi": "स्थिर एवं द्विस्वभाव लग्न में कार्यालय आरंभ उत्तम है।"
      },
      "required_details": {
        "en": "Founder/firm name, office type, address, and date range.",
        "te": "వ్యవస్థాపకుని పేరు, ఆఫీసు రకం, చిరునామా, తేదీల పరిధి.",
        "hi": "संस्थापक का नाम, कार्यालय का प्रकार, पता एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "business-commencement": {
    "slug": "business-commencement",
    "legacy_slug": "business-commencement",
    "icon": "🚀",
    "category": "business_career",
    "title": {
      "en": "New Business Commencement Muhurtham",
      "te": "నూతన వ్యాపార ఆరంభ ముహూర్తం (Business Commencement)",
      "hi": "व्यापारिक कार्य आरंभ मुहूर्त (Business Commencement)"
    },
    "short_desc": {
      "en": "Auspicious timings for executing first business transactions, billing, and kickoff operations.",
      "te": "మొదటి బిల్లింగ్, వ్యాపార లావాదేవీలు ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "प्रथम बिलिंग एवं व्यावहारिक कार्य आरंभ करने का शुभ मुहूर्त।"
    },
    "form_schema": "business_commencement",
    "content": {
      "about": {
        "en": "Commencing initial commercial transactions or cutting the ceremonial ribbon on a new enterprise lays the karmic seed for future corporate growth.",
        "te": "మొట్టమొదటి ఇన్వాయిస్ వేసే లేదా ఉత్పత్తిని ప్రారంభించే సమయం వ్యాపార విజయానికి పునాది.",
        "hi": "व्यापार का वास्तविक कार्य एवं प्रथम बिलिंग शुभ मुहूर्त में करना कल्याणकारी है।"
      },
      "religious_significance": {
        "en": "Sri Sukta recitation invokes the golden touch of Mahalakshmi on all ledger books.",
        "te": "శ్రీసూక్త పారాయణంతో ఖాతా పుస్తకాలకు లక్ష్మీ కటాక్షం లభిస్తుంది.",
        "hi": "श्री सूक्त के पाठ से व्यापार में लक्ष्मी का वास होता है।"
      },
      "why_muhurtham": {
        "en": "Prevents operational bottlenecks, regulatory freezes, and partnership friction.",
        "te": "పనుల్లో ఆటంకాలు లేకుండా వ్యాపారం వేగంగా అభివృద్ధి చెందుతుంది.",
        "hi": "व्यवसाय में निरंतर गति और लाभ हेतु मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Pushya, Rohini, Hasta, and Anuradha are best. Mercury horas are favorable.",
        "te": "పుష్యమి, రోహిణి, హస్త నక్షత్రాలు మరియు బుధ హోర అనుకూలం.",
        "hi": "पुष्य, हस्त नक्षत्र एवं बुध की होरा में शुभारंभ करें।"
      },
      "required_details": {
        "en": "Founder name, company name, commercial city, and date range.",
        "te": "వ్యవస్థాపకుని పేరు, కంపెనీ పేరు, నగరం, తేదీల పరిధి.",
        "hi": "संस्थापक का नाम, कंपनी, नगर एवं दिनांक সীমা।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "job-joining": {
    "slug": "job-joining",
    "legacy_slug": "job-joining",
    "icon": "💼",
    "category": "business_career",
    "title": {
      "en": "Job Joining Muhurtham",
      "te": "ఉద్యోగ ప్రవేశ ముహూర్తం (Job Joining)",
      "hi": "नौकरी जॉइनिंग मुहूर्त (Job Joining)"
    },
    "short_desc": {
      "en": "Auspicious timing for reporting to duty and taking official charge of a new position.",
      "te": "నూతన ఉద్యోగంలో చేరడానికి, విధుల్లో బాధ్యతలు స్వీకరించడానికి శుభ ముహూర్తాలు.",
      "hi": "नई नौकरी में पदभार ग्रहण करने का शुभ मुहूर्त।"
    },
    "form_schema": "job_joining",
    "content": {
      "about": {
        "en": "Reporting to duty on an auspicious hour fosters career advancement, harmonious relations with superiors, job security, and rapid promotions.",
        "te": "మంచి ముహూర్తంలో ఉద్యోగంలో చేరితే ప్రమోషన్లు, అధికారుల మన్ననలు లభిస్తాయి.",
        "hi": "शुभ समय में पदभार ग्रहण करने से पदोन्नति, अधिकारियों का सहयोग और स्थिरता मिलती है।"
      },
      "religious_significance": {
        "en": "Surya (Sun) governs authority and professional status; joining under Sun's benefic aspect elevates status.",
        "te": "రవి అనుగ్రహంతో పదవిలో ఉన్నత స్థానాలు, గౌరవం లభిస్తాయి.",
        "hi": "सूर्य देव की कृपा से कार्यक्षेत्र में प्रतिष्ठा और नेतृत्व प्राप्त होता है।"
      },
      "why_muhurtham": {
        "en": "Shields against office politics, toxic managers, and sudden job termination.",
        "te": "కార్యాలయంలో రాజకీయాలు, ఒత్తిడి లేకుండా సుస్థిరత ఉంటుంది.",
        "hi": "कार्यक्षेत्र में स्थिरता और कलह से मुक्ति मिलती है।"
      },
      "selection_principles": {
        "en": "Morning office reporting hours are factored in, strictly excluding Rahu Kalam.",
        "te": "కార్యాలయ వేళలలో రాహుకాలం లేని సమయం ఎంచుకుంటారు.",
        "hi": "प्रातःकाल का शुद्ध समय चयन किया जाता है।"
      },
      "required_details": {
        "en": "User name, designation/role, office location, joining date constraints, and preferred time.",
        "te": "అభ్యర్థి పేరు, ఉద్యోగ వివరాలు, స్థలం, చేరవలసిన తేదీల పరిధి.",
        "hi": "नाम, पदनाम, कार्यालय स्थल, दिनांक सीमा एवं समय।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        8,
        12,
        13,
        14,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "project-commencement": {
    "slug": "project-commencement",
    "legacy_slug": "project-commencement",
    "icon": "📊",
    "category": "business_career",
    "title": {
      "en": "Project Commencement Muhurtham",
      "te": "ప్రాజెక్ట్ ప్రారంభ ముహూర్తం (Project Commencement)",
      "hi": "परियोजना शुभारंभ मुहूर्त (Project Commencement)"
    },
    "short_desc": {
      "en": "Auspicious timing for project kickoff meetings, software releases, and client deliverables.",
      "te": "కీలక ప్రాజెక్టులు, సాఫ్ట్‌వేర్ లాంచ్‌లు ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "नई परियोजना, सॉफ्टवेयर लॉन्च एवं कार्य शुभारंभ का शुभ मुहूर्त।"
    },
    "form_schema": "project_commencement",
    "content": {
      "about": {
        "en": "Initiating major commercial projects or technology deployments in an auspicious Muhurtham guarantees timely delivery within budget and stakeholder satisfaction.",
        "te": "ప్రాజెక్ట్ ప్రారంభం సరైన సమయంలో జరిగితే ఆటంకాలు లేకుండా అనుకున్న గడువులో పూర్తవుతుంది.",
        "hi": "शुभ समय में परियोजना का आरंभ समय पर पूर्णता और सफलता की गारंटी देता है।"
      },
      "religious_significance": {
        "en": "Lord Ganesha ensures all technical and logistical hurdles are dissolved.",
        "te": "విఘ్నేశ్వరుని అనుగ్రహంతో సాంకేతిక సమస్యలు పరిష్కారమవుతాయి.",
        "hi": "गणेश जी की कृपा से परियोजना निर्विघ्न संपन्न होती है।"
      },
      "why_muhurtham": {
        "en": "Averts scope creep, technical bugs, and client dissatisfaction.",
        "te": "క్లయింట్ సంతృప్తి, నాణ్యమైన ఫలితాలు సాధించడానికి ముహూర్తం ఉపకరిస్తుంది.",
        "hi": "विवादों और तकनीकी रुकावटों से बचाव होता है।"
      },
      "selection_principles": {
        "en": "Mercury and Jupiter ruled hours in Shukla Paksha are prioritized.",
        "te": "బుధ, గురు హోరలు మరియు శుక్ల పక్షం అనుకూలం.",
        "hi": "बुध और गुरु के शुभ प्रभाव में कार्य प्रारंभ करें।"
      },
      "required_details": {
        "en": "Project lead name, project domain, team location, and date range.",
        "te": "ప్రాజెక్ట్ లీడ్ పేరు, ప్రాజెక్ట్ రకం, స్థలం, తేదీల పరిధి.",
        "hi": "नाम, परियोजना का नाम, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "contract-signing": {
    "slug": "contract-signing",
    "legacy_slug": "contract-signing",
    "icon": "✒️",
    "category": "business_career",
    "title": {
      "en": "Contract Signing Muhurtham",
      "te": "ఒప్పంద సంతకం ముహూర్తం (Contract Signing)",
      "hi": "अनुबंध हस्ताक्षर मुहूर्त (Contract Signing)"
    },
    "short_desc": {
      "en": "Auspicious timings for signing business agreements, commercial contracts, and MoUs.",
      "te": "వ్యాపార ఒప్పందాలు, ఎంఓయూలపై సంతకాలు చేయడానికి శుభ ముహూర్తాలు.",
      "hi": "व्यावसायिक समझौतों एवं अनुबंध पर हस्ताक्षर हेतु शुभ मुहूर्त।"
    },
    "form_schema": "contract_signing",
    "content": {
      "about": {
        "en": "Affixing signatures to binding agreements in an unblemished Lagna ensures both parties fulfill obligations honorably and profitably.",
        "te": "శుభ సమయంలో ఒప్పందం కుదుర్చుకుంటే ఇరుపక్షాలకు లాభాలు చేకూరి వివాదాలు రావు.",
        "hi": "शुभ समय में अनुबंध करने से व्यापारिक संबंध प्रगाढ़ और लाभकारी होते हैं।"
      },
      "religious_significance": {
        "en": "Mercury and Jupiter invoke truthfulness, legal protection, and prosperity.",
        "te": "బుధ, గురుల అనుగ్రహం న్యాయపరమైన రక్షణను ప్రసాదిస్తుంది.",
        "hi": "बुध और गुरु व्यापार में पारदर्शिता और लाभ प्रदान करते हैं।"
      },
      "why_muhurtham": {
        "en": "Averts breach of contract, hidden arbitration clauses, and broken promises.",
        "te": "భవిష్యత్తులో నమ్మకద్రోహం, నష్టాలు జరగకుండా కాపాడుతుంది.",
        "hi": "विश्वासघात और आर्थिक हानि से रक्षा होती है।"
      },
      "selection_principles": {
        "en": "Mercury ruled hours and Shukla Paksha are favored.",
        "te": "బుధ హోర మరియు శుక్ల పక్షం అనుకూలం.",
        "hi": "शुक्ल पक्ष एवं बुध के शुभ प्रभाव ग्राह्य हैं।"
      },
      "required_details": {
        "en": "Signee name, business details, location, and date range.",
        "te": "సంతకందారు పేరు, ఒప్పంద వివరాలు, స్థలం, తేదీల పరిధి.",
        "hi": "हस्ताक्षरकर्ता का नाम, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        8,
        13,
        15,
        17,
        22,
        23,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "agreement-signing": {
    "slug": "agreement-signing",
    "legacy_slug": "agreement-signing",
    "icon": "✍️",
    "category": "business_career",
    "title": {
      "en": "Agreement Signing Muhurtham",
      "te": "అగ్రిమెంట్ సంతకాల ముహూర్తం (Agreement Signing)",
      "hi": "समझौता हस्ताक्षर मुहूर्त (Agreement Signing)"
    },
    "short_desc": {
      "en": "Auspicious timing for rental agreements, partnership deeds, and lease covenants.",
      "te": "లీజులు, భాగస్వామ్య పత్రాలు, అగ్రిమెంట్లపై సంతకాలు చేయడానికి ముహూర్తాలు.",
      "hi": "किरायानामा, साझेदारी विलेख एवं समझौतों पर हस्ताक्षर का शुभ मुहूर्त।"
    },
    "form_schema": "agreement_signing",
    "content": {
      "about": {
        "en": "Signing rental agreements, lease deeds, and partnership compacts in an auspicious hour establishes mutual transparency and peaceful tenure.",
        "te": "భాగస్వామ్య మరియు లీజు ఒప్పందాలు శాంతియుతంగా కొనసాగడానికి ఈ ముహూర్తం ఎంచుకుంటారు.",
        "hi": "साझेदारी विलेख एवं समझौतों पर शुभ समय में हस्ताक्षर परस्पर सौहार्द बनाए रखता है।"
      },
      "religious_significance": {
        "en": "Brihaspati maintains righteousness and harmonious balance between counterparties.",
        "te": "బృహస్పతి అనుగ్రహంతో ధర్మబద్ధమైన సంబంధాలు వర్ధిల్లుతాయి.",
        "hi": "बृहस्पति देव की कृपा से समझौते में पारदर्शिता रहती है।"
      },
      "why_muhurtham": {
        "en": "Prevents bitter legal litigation, tenancy disputes, and unpaid damages.",
        "te": "భవిష్యత్తులో గొడవలు రాకుండా పత్రాలు సవ్యంగా ఉండటానికి సహాయపడుతుంది.",
        "hi": "कानूनी मुकदमों और विवादों से बचाव होता है।"
      },
      "selection_principles": {
        "en": "Avoid Rikta Tithis and Saturdays. Favorable Nakshatras include Rohini, Hasta, and Revati.",
        "te": "రోహిణి, హస్త, రేవతి నక్షత్రాలు అనుకూలం.",
        "hi": "रोहिणी, हस्त, रेवती नक्षत्र शुभ हैं।"
      },
      "required_details": {
        "en": "Parties involved, agreement type, signing venue, and date range.",
        "te": "ఇరు పక్షాల పేర్లు, ఒప్పందం రకం, సంతకం చేసే నగరం, తేదీల పరిధి.",
        "hi": "पक्षकारों का नाम, समझौते का प्रकार, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "loan-application": {
    "slug": "loan-application",
    "legacy_slug": "loan-application",
    "icon": "🏦",
    "category": "business_career",
    "title": {
      "en": "Loan Application Muhurtham",
      "te": "రుణ దరఖాస్తు ముహూర్తం (Loan Application)",
      "hi": "ऋण / लोन आवेदन मुहूर्त (Loan Application)"
    },
    "short_desc": {
      "en": "Auspicious timings for applying for home loans, business financing, and mortgages.",
      "te": "గృహ రుణాలు, వ్యాపార లోన్లకు దరఖాస్తు చేసుకోవడానికి అనుకూల ముహూర్తాలు.",
      "hi": "गृह ऋण, व्यावसायिक ऋण आवेदन हेतु शास्त्रोक्त शुभ मुहूर्त।"
    },
    "form_schema": "loan_application",
    "content": {
      "about": {
        "en": "Submitting loan applications during an auspicious astrological hour ensures smooth bank approval and seamless debt repayment without financial distress.",
        "te": "రుణం కోసం దరఖాస్తు చేసుకునేటప్పుడు సరైన ముహూర్తం చూస్తే లోన్ త్వరగా మంజూరై, సకాలంలో తీరిపోతుంది.",
        "hi": "शुभ मुहूर्त में ऋण आवेदन करने से बैंक से स्वीकृति शीघ्र मिलती है और ऋण समय पर चुकता होता है।"
      },
      "religious_significance": {
        "en": "Propitiates Lord Kubera and Moon to maintain debt solvency.",
        "te": "కుబేరుని అనుగ్రహంతో అప్పుల ఊబిలో పడకుండా సంపద నిలుస్తుంది.",
        "hi": "कुबेर देव की कृपा से ऋण भार से शीघ्र मुक्ति मिलती है।"
      },
      "why_muhurtham": {
        "en": "Prevents crushing interest burdens, bureaucratic rejections, and bad debts.",
        "te": "రుణం సకాలంలో తీరి ఆర్థిక ప్రశాంతత లభించడానికి ముహూర్తం ముఖ్యం.",
        "hi": "अत्यधिक ब्याज और कर्ज के जाल में फंसने से रक्षा होती है।"
      },
      "selection_principles": {
        "en": "Tuesday is strictly avoided for taking loans (Tuesday loans become difficult to repay). Thursdays and Fridays are favored.",
        "te": "మంగళవారం అప్పు తీసుకోకూడదు; గురు, శుక్రవారాలు దరఖాస్తుకు అనుకూలం.",
        "hi": "मंगलवार को कर्ज लेने से बचें; गुरुवार एवं शुक्रवार प्रशस्त हैं।"
      },
      "required_details": {
        "en": "Applicant name, loan category, bank location, and date range.",
        "te": "దరఖాస్తుదారు పేరు, లోన్ రకం, బ్యాంక్ స్థలం, తేదీల పరిధి.",
        "hi": "आवेदक का नाम, ऋण का प्रकार, बैंक स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "account-opening": {
    "slug": "account-opening",
    "legacy_slug": "account-opening",
    "icon": "💳",
    "category": "business_career",
    "title": {
      "en": "Account Opening Muhurtham",
      "te": "బ్యాంకు ఖాతా ప్రారంభ ముహూర్తం (Account Opening)",
      "hi": "बैंक खाता खोलने का मुहूर्त (Account Opening)"
    },
    "short_desc": {
      "en": "Auspicious timings for opening savings accounts, current accounts, and treasury lockers.",
      "te": "పొదుపు ఖాతాలు, కరెంట్ అకౌంట్లు, బ్యాంక్ లాకర్లు ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "बचत खाता, चालू खाता एवं बैंक लॉकर खोलने का शुभ मुहूर्त।"
    },
    "form_schema": "account_opening",
    "content": {
      "about": {
        "en": "Opening bank accounts and bank lockers in an auspicious cosmic hora attracts steady money deposits and wealth preservation.",
        "te": "బ్యాంకు ఖాతా లేదా లాకర్ తెరిచే సమయం బాగుంటే అందులో ధనం ఎల్లప్పుడూ నిల్వ ఉంటుంది.",
        "hi": "शुभ समय में बैंक खाता खोलने से धन की बचत और वृद्धि होती है।"
      },
      "religious_significance": {
        "en": "Sri Mahalakshmi is invited to establish permanent residence in the financial ledger.",
        "te": "లక్ష్మీదేవి కొలువై ఉండి ఖాతాలో ధన ప్రవాహం స్థిరంగా సాగుతుంది.",
        "hi": "मां महालक्ष्मी की कृपा से खाते में निरंतर धन वृद्धि होती है।"
      },
      "why_muhurtham": {
        "en": "Prevents empty accounts, overdrafts, bank frauds, and unnecessary expenses.",
        "te": "ఖాతాలో ఎప్పుడూ ధనం ఉండేలా, వృథా ఖర్చులు కాకుండా కాపాడుతుంది.",
        "hi": "अनावश्यक व्यय रुकता है और बचत बढ़ती है।"
      },
      "selection_principles": {
        "en": "Pushya, Dhanishta, Rohini, and Revati Nakshatras on Thursday or Friday are best.",
        "te": "పుష్యమి, రోహిణి, ధనిష్ఠ నక్షత్రాలు, గురు-శుక్రవారాలు అత్యుత్తమం.",
        "hi": "पुष्य, रोहिणी नक्षत्र एवं गुरुवार-शुक्रवार सर्वोत्तम हैं।"
      },
      "required_details": {
        "en": "Account holder name, account type, bank branch city, and date range.",
        "te": "ఖాతాదారు పేరు, ఖాతా రకం, బ్యాంక్ నగరం, తేదీల పరిధి.",
        "hi": "खाताधारक का नाम, प्रकार, नगर एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "financial-beginning": {
    "slug": "financial-beginning",
    "legacy_slug": "financial-beginning",
    "icon": "💰",
    "category": "business_career",
    "title": {
      "en": "Financial Beginning Muhurtham",
      "te": "ఆర్థిక పెట్టుబడుల ముహూర్తం (Financial Beginning)",
      "hi": "वित्तीय शुभारंभ मुहूर्त (Financial Beginning)"
    },
    "short_desc": {
      "en": "Auspicious timings for major capital investments, wealth deposits, and wealth creation.",
      "te": "పెద్ద మొత్తంలో పెట్టుబడులు, డిపాజిట్లు ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "बड़ा निवेश, सावधि जमा एवं वित्तीय शुभारंभ का शुभ मुहूर्त।"
    },
    "form_schema": "financial_beginning",
    "content": {
      "about": {
        "en": "Commencing investments and capital allocations in an auspicious hour multiplies wealth and averts speculative losses.",
        "te": "శుభ సమయంలో చేసే పెట్టుబడులు వృద్ధి చెంది సంపదను పెంచుతాయి.",
        "hi": "शुभ समय में किया गया निवेश निरंतर बढ़ता है और धन हानि नहीं होती।"
      },
      "religious_significance": {
        "en": "Invokes Mahalakshmi and Kubera for financial abundance and sound judgment.",
        "te": "లక్ష్మీదేవి, కుబేరుల అనుగ్రహం కలిగి ధనలాభం సమకూరుతుంది.",
        "hi": "मां लक्ष्मी एवं कुबेर देव की कृपा से धन-धान्य की वृद्धि होती है।"
      },
      "why_muhurtham": {
        "en": "Prevents money from becoming stuck or lost in fraudulent ventures.",
        "te": "ధనం నిలిచిపోకుండా లాభసాటిగా తిరిగి రావడానికి ముహూర్తం అవసరం.",
        "hi": "धन की सुरक्षा और निरंतर लाभ हेतु मुहूर्त विचार अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Dhanishta, Pushya, Rohini, Revati, and Ashwini are prime. Thursday is favored.",
        "te": "ధనిష్ఠ, పుష్యమి, రోహిణి, రేవతి నక్షత్రాలు శ్రేష్ఠం.",
        "hi": "धनिष्ठा, पुष्य, रोहिणी नक्षत्र उत्तम हैं।"
      },
      "required_details": {
        "en": "Investor details, investment type, location, and date range.",
        "te": "పెట్టుబడిదారు వివరాలు, రకం, స్థలం, తేదీల పరిధి.",
        "hi": "निवेशक का विवरण एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        8,
        12,
        13,
        15,
        17,
        22,
        23,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5
      ],
      "prohibited_weekdays": [
        2,
        6
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "trading-commencement": {
    "slug": "trading-commencement",
    "legacy_slug": "trading-commencement",
    "icon": "📈",
    "category": "business_career",
    "title": {
      "en": "Trading / Investment Commencement Muhurtham",
      "te": "ట్రేడింగ్ ప్రారంభ ముహూర్తం (Trading Commencement)",
      "hi": "ट्रेडिंग एवं शेयर बाजार मुहूर्त (Trading Commencement)"
    },
    "short_desc": {
      "en": "Auspicious timing for opening Demat accounts, Muhurat trading, and starting stock market investments.",
      "te": "డీమ్యాట్ ఖాతాలు, షేర్ మార్కెట్ ట్రేడింగ్ ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "शेयर बाजार, ट्रेडिंग एवं डीमैट खाता खोलने का शुभ मुहूर्त।"
    },
    "form_schema": "trading_commencement",
    "content": {
      "about": {
        "en": "Commencing stock market trading, commodities, or Demat allocations under favorable astrological planetary horas shields against rash speculation and brings financial prudence.",
        "te": "స్టాక్ మార్కెట్, ట్రేడింగ్ పనులను సరైన సమయంలో ప్రారంభిస్తే లాభాలు వచ్చే అవకాశాలు పెరుగుతాయి.",
        "hi": "शेयर बाजार में ट्रेडिंग का शुभारंभ शुभ मुहूर्त में करने से विवेक और लाभ बढ़ता है।"
      },
      "religious_significance": {
        "en": "Mercury (Budha - planet of trade and calculation) and Jupiter are invoked for intellect.",
        "te": "వ్యాపార కారకుడైన బుధ గ్రహ అనుగ్రహంతో సరైన నిర్ణయాలు తీసుకుంటారు.",
        "hi": "बुध ग्रह की कृपा से व्यापारिक बुद्धि और दूरदर्शिता प्राप्त होती है।"
      },
      "why_muhurtham": {
        "en": "Wards off disastrous panic sell-offs, margin calls, and speculative wipeouts.",
        "te": "ఆవేశంలో తప్పుడు నిర్ణయాలు తీసుకోకుండా మనస్సును నిలకడగా ఉంచుతుంది.",
        "hi": "भारी घाटे और सट्टेबाजी के नुकसान से रक्षा होती है।"
      },
      "selection_principles": {
        "en": "Mercury horas on Wednesdays and Thursdays with favorable lunar Nakshatras.",
        "te": "బుధ, గురువారాలు మరియు బుధ హోరలు అత్యంత అనుకూలం.",
        "hi": "बुधवार एवं गुरुवार को बुध की शुभ होरा में प्रारंभ करें।"
      },
      "required_details": {
        "en": "Trader name, exchange category, city, and date range.",
        "te": "ట్రేడర్ పేరు, మార్కెట్ రకం, నగరం, తేదీల పరిధి.",
        "hi": "व्यापारी का नाम, बाजार का प्रकार एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "general-puja": {
    "slug": "general-puja",
    "legacy_slug": "general-puja",
    "icon": "🪔",
    "category": "puja_religious",
    "title": {
      "en": "General Puja Muhurtham",
      "te": "సాధారణ పూజ ముహూర్తం (General Puja)",
      "hi": "सामान्य पूजा एवं अनुष्ठान मुहूर्त (General Puja)"
    },
    "short_desc": {
      "en": "Auspicious timing for daily rituals, special archanas, and deity worship.",
      "te": "ఇష్టదైవ పూజలు, విశేష అర్చనలు, వ్రతాలు నిర్వహించడానికి శుభ ముహూర్తాలు.",
      "hi": "इष्टदेव पूजन, विशेष अर्चना एवं देव आराधना का शुभ मुहूर्त।"
    },
    "form_schema": "general_puja",
    "content": {
      "about": {
        "en": "Conducting worship of one's Ishta Devata during an auspicious hora connects the devotee directly with divine grace, generating spiritual peace and merit (Punya).",
        "te": "ఇష్టదైవాన్ని శుభ ముహూర్తంలో ఆరాధిస్తే సంపూర్ణ భక్తిఫలం దక్కి మనశ్శాంతి కలుగుతుంది.",
        "hi": "शुभ समय में इष्टदेव की पूजा करने से पूजा का पूर्ण फल एवं आत्मिक शांति प्राप्त होती है।"
      },
      "religious_significance": {
        "en": "Vedic pujas cleanse internal impurities and harmonize subtle planetary influences.",
        "te": "పూజా విధి ద్వారా అంతఃకరణ శుద్ధి జరిగి గ్రహదోషాలు తొలగిపోతాయి.",
        "hi": "देव पूजन से मन की शुद्धि एवं पापों का शमन होता है।"
      },
      "why_muhurtham": {
        "en": "Ensures the mind remains serene, concentrated, and free from wandering distractions during worship.",
        "te": "పూజలో ఏకాగ్రత కుదిరి దైవ సాక్షాత్కారం కలగడానికి ముహూర్తం ముఖ్యం.",
        "hi": "पूजा में एकाग्रता और भक्ति भाव बनाए रखने हेतु शुभ काल आवश्यक है।"
      },
      "selection_principles": {
        "en": "Brahma Muhurtham and morning Sandhya horas are universally supreme.",
        "te": "బ్రహ్మ ముహూర్తం మరియు ప్రాతః సంధ్యా వేళలు అత్యంత శ్రేష్ఠమైనవి.",
        "hi": "ब्रह्म मुहूर्त एवं प्रातःकाल की वेला पूजा हेतु सर्वोत्तम है।"
      },
      "required_details": {
        "en": "Devotee name, deity worshipped, location, and date range.",
        "te": "భక్తుని పేరు, ఇష్టదైవం, ప్రదేశం, తేదీల పరిధి.",
        "hi": "भक्त का नाम, देवता का नाम, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "griha-shanti": {
    "slug": "griha-shanti",
    "legacy_slug": "griha-shanti",
    "icon": "🕊️",
    "category": "puja_religious",
    "title": {
      "en": "Griha Shanti Muhurtham",
      "te": "గృహ శాంతి ముహూర్తం (Griha Shanti)",
      "hi": "गृह शांति एवं वास्तु शुद्धि मुहूर्त (Griha Shanti)"
    },
    "short_desc": {
      "en": "Auspicious timings for home purification, Vastu Shanti, and removing negative energies.",
      "te": "ఇంట్లో అశాంతి, వాస్తు దోషాలు తొలగించే గృహ శాంతి పూజ ముహూర్తాలు.",
      "hi": "घर में सुख-शांति एवं वास्तु दोष निवारण हेतु गृह शांति मुहूर्त।"
    },
    "form_schema": "griha_shanti",
    "content": {
      "about": {
        "en": "Griha Shanti homams neutralize hidden Vastu doshas, evil eye, and environmental negativity, restoring domestic tranquility and harmony.",
        "te": "ఇంట్లో కలహాలు, అశాంతి ఉన్నప్పుడు వాస్తు దోష నివారణకు గృహ శాంతి నిర్వహిస్తారు.",
        "hi": "गृह क्लेश और नकारात्मक ऊर्जा को दूर करने हेतु गृह शांति अनुष्ठान किया जाता है।"
      },
      "religious_significance": {
        "en": "Invokes Vastu Purusha, Navagrahas, and Kshetrapala with holy water sprinkles (Punyahavachanam).",
        "te": "పుణ్యాహవాచనం, నవగ్రహ హోమాలతో ఇల్లంతా పవిత్రమవుతుంది.",
        "hi": "वास्तु पुरुष और नवग्रहों की शांति से घर में सकारात्मक ऊर्जा लौटती है।"
      },
      "why_muhurtham": {
        "en": "Restores harmonious sleep, family affection, and good health to all inhabitants.",
        "te": "కుటుంబ సభ్యులకు ఆరోగ్యం, ప్రశాంతమైన నిద్ర చేకూరడానికి ఈ పూజ చేస్తారు.",
        "hi": "पारिवारिक सौहार्द और स्वास्थ्य लाभ हेतु शुभ समय आवश्यक है।"
      },
      "selection_principles": {
        "en": "Shukla Paksha days free from Rahu Kalam and Gulika are preferred.",
        "te": "శుక్ల పక్ష దినాలు, రాహుకాలం లేని సమయాలు అనుకూలం.",
        "hi": "शुक्ल पक्ष एवं शुद्ध लग्न में गृह शांति की जानी चाहिए।"
      },
      "required_details": {
        "en": "House owner name, home address, Shanti purpose, and date range.",
        "te": "యజమాని పేరు, ఇంటి చిరునామా, కారణం, తేదీల పరిధి.",
        "hi": "स्वामी का नाम, पता, उद्देश्य एवं दिनांक সীমা।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "ganapati-homam": {
    "slug": "ganapati-homam",
    "legacy_slug": "ganapati-homam",
    "icon": "🐘",
    "category": "puja_religious",
    "title": {
      "en": "Ganapati Homam Muhurtham",
      "te": "గణపతి హోమం ముహూర్తం (Ganapati Homam)",
      "hi": "गणपति होम / हवन मुहूर्त (Ganapati Homam)"
    },
    "short_desc": {
      "en": "Auspicious timing for Lord Ganesha fire rituals to eliminate obstacles.",
      "te": "సర్వ విఘ్నాలు తొలగి శుభాలు కలగడానికి గణపతి హోమ ముహూర్తాలు.",
      "hi": "समस्त विघ्नों के निवारण हेतु महागणपति होम का शुभ मुहूर्त।"
    },
    "form_schema": "ganapati_homam",
    "content": {
      "about": {
        "en": "Maha Ganapati Homam is the premier Vedic fire ritual performed before commencing any significant worldly or spiritual endeavor to eliminate obstacles.",
        "te": "ఏ శుభకార్యానికైనా విఘ్నాలు రాకుండా ముందుగా చేసే ప్రధాన యజ్ఞం గణపతి హోమం.",
        "hi": "किसी भी कार्य के निर्विघ्न समापन हेतु महागणपति होम सर्वप्रथम किया जाता है।"
      },
      "religious_significance": {
        "en": "Lord Ganesha as Vighnaharta clears obstacles from the past, present, and future.",
        "te": "గణపతి భగవానుడు సమస్త గ్రహబాధలను, ఆటంకాలను దూరం చేస్తాడు.",
        "hi": "विघ्नहर्ता गणेश जी सभी ज्ञात-अज्ञात बाधाओं का शमन करते हैं।"
      },
      "why_muhurtham": {
        "en": "Guarantees the ritual produces swift positive breakthroughs and peace of mind.",
        "te": "హోమ ఫలితం శీఘ్రంగా దక్కి అనుకున్న కార్యాలు విజయవంతమవుతాయి.",
        "hi": "हवन का त्वरित और शुभ फल प्राप्त करने हेतु मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Early morning Brahma/Sunrise hours. Shukla Chaturthi, Sankashti, or benefic weekdays.",
        "te": "ఉదయపు వేళలు, శుక్ల చతుర్థి లేదా బుధ, గురువారాలు శ్రేష్ఠం.",
        "hi": "प्रातःकाल, शुक्ल पक्ष एवं बुधवार को गणपति होम अत्यंत फलदायी है।"
      },
      "required_details": {
        "en": "Devotee name, specific intention (Sankalpa), venue, and date range.",
        "te": "భక్తుని పేరు, సంకల్పం, వేదిక, తేదీల పరిధి.",
        "hi": "यजमान का नाम, संकल्प, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "navagraha-shanti": {
    "slug": "navagraha-shanti",
    "legacy_slug": "navagraha-shanti",
    "icon": "🪐",
    "category": "puja_religious",
    "title": {
      "en": "Navagraha Shanti Muhurtham",
      "te": "నవగ్రహ శాంతి ముహూర్తం (Navagraha Shanti)",
      "hi": "नवग्रह शांति एवं होम मुहूर्त (Navagraha Shanti)"
    },
    "short_desc": {
      "en": "Auspicious timings for pacifying malefic planetary transits, Sade Sati, and Dasha doshas.",
      "te": "గ్రహదోషాలు, ఏలినాటి శని, దశ దోష నివారణకు నవగ్రహ శాంతి ముహూర్తాలు.",
      "hi": "ग्रह दोष, साढ़ेसाती एवं दशा शांति हेतु नवग्रह होम का शुभ मुहूर्त।"
    },
    "form_schema": "navagraha_shanti",
    "content": {
      "about": {
        "en": "Navagraha Shanti Homam pacifies afflictions from unfavorable planetary positions, transits (Gochara), Sade Sati, or adverse Dasha-Antardasha periods.",
        "te": "జాతకంలోని గ్రహదోషాలు, అశుభ గోచార ప్రభావాలను శాంతింపజేసే పూజ నవగ్రహ శాంతి.",
        "hi": "कुंडली के ग्रह दोषों एवं प्रतिकूल गोचर के शमन हेतु नवग्रह शांति की जाती है।"
      },
      "religious_significance": {
        "en": "Propitiates Surya, Chandra, Mangala, Budha, Guru, Shukra, Shani, Rahu, and Ketu with specific Samidhas.",
        "te": "తొమ్మిది గ్రహాలకు ప్రత్యేక సమిధలతో ఆహుతులు ఇచ్చి అనుగ్రహాన్ని పొందుతారు.",
        "hi": "नवग्रहों की समिधाओं से हवन कर उनकी कृपा प्राप्त की जाती है।"
      },
      "why_muhurtham": {
        "en": "Alleviates recurring physical ailments, financial stress, and mental anxiety.",
        "te": "ఆరోగ్య సమస్యలు, మానసిక ఆందోళనలు తొలగి గ్రహానుకూలత కలుగుతుంది.",
        "hi": "मानसिक तनाव और कष्टों से मुक्ति हेतु मुहूर्त विचार किया जाता है।"
      },
      "selection_principles": {
        "en": "Coincides with native's favorable Tarabala day, strictly avoiding inauspicious Karanas.",
        "te": "జాతకుడి తారాబలం మరియు అనుకూల వారం చూసుకుని నిర్వహిస్తారు.",
        "hi": "अनुकूल ताराबल और शुभ दिन पर नवग्रह शांति श्रेष्ठ है।"
      },
      "required_details": {
        "en": "Native name, afflicted planet / Dasha, location, and date range.",
        "te": "వ్యక్తి పేరు, బాధిస్తున్న గ్రహం, వేదిక, తేదీల పరిధి.",
        "hi": "जातक का नाम, पीड़ित ग्रह/दशा, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "sudarshana-homam": {
    "slug": "sudarshana-homam",
    "legacy_slug": "sudarshana-homam",
    "icon": "☸️",
    "category": "puja_religious",
    "title": {
      "en": "Sudarshana Homam Muhurtham",
      "te": "సుదర్శన హోమం ముహూర్తం (Sudarshana Homam)",
      "hi": "सुदर्शन होम मुहूर्त (Sudarshana Homam)"
    },
    "short_desc": {
      "en": "Auspicious timing for divine protection, health recovery, and conquering negativity.",
      "te": "శత్రుభయాలు, అనారోగ్య నివారణ, సర్వ రక్షణ కొరకు సుదర్శన హోమ ముహూర్తాలు.",
      "hi": "आरोग्य लाभ, शत्रु बाधा निवारण एवं सर्व रक्षा हेतु सुदर्शन होम मुहूर्त।"
    },
    "form_schema": "sudarshana_homam",
    "content": {
      "about": {
        "en": "Sudarshana Homam invokes the divine weapon of Lord Maha Vishnu—the burning Sudarshana Chakra—to destroy negative forces, severe illnesses, and evil spells.",
        "te": "శ్రీ మహావిష్ణువు సుదర్శన చక్రాన్ని ఆవాహన చేసి సమస్త దుష్ట శక్తులు, వ్యాధులను పారద్రోలే మహా యజ్ఞం.",
        "hi": "भगवान विष्णु के सुदर्शन चक्र का आह्वान कर समस्त नकारात्मक शक्तियों का नाश किया जाता है।"
      },
      "religious_significance": {
        "en": "Combines Sudarshana and Narasimha Mantras for an impregnable protective armor (Kavacham).",
        "te": "సుదర్శన మరియు నరసింహ మంత్రాల శక్తితో సర్వ రక్ష ఏర్పడుతుంది.",
        "hi": "सुदर्शन-नृसिंह मंत्रों की आहुतियों से अभेद्य सुरक्षा कवच बनता है।"
      },
      "why_muhurtham": {
        "en": "Provides rapid relief from prolonged diseases, black magic fear, and unexplained setbacks.",
        "te": "దీర్ఘకాలిక రోగాలు, శత్రుపీడల నుండి విముక్తి పొందడానికి ఈ హోమం చేస్తారు.",
        "hi": "रोग मुक्ति एवं मानसिक भय निवारण हेतु शुभ लग्न में होम करें।"
      },
      "selection_principles": {
        "en": "Ekadashi, Dwadashi, Poornima, or Wednesdays/Saturdays with Vaishnava nakshatras (Shravana, Swati).",
        "te": "ఏకాదశి, పూర్ణిమ, శ్రవణం లేదా స్వాతి నక్షత్రాలు అనుకూలం.",
        "hi": "एकादशी, पूर्णिमा एवं श्रवण नक्षत्र में सुदर्शन होम श्रेष्ठ है।"
      },
      "required_details": {
        "en": "Devotee name, protection purpose, temple or home venue, and date range.",
        "te": "భక్తుని పేరు, సంకల్పం, ప్రదేశం, తేదీల పరిధి.",
        "hi": "भक्त का नाम, संकल्प, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "rudrabhishekam": {
    "slug": "rudrabhishekam",
    "legacy_slug": "rudrabhishekam",
    "icon": "🔱",
    "category": "puja_religious",
    "title": {
      "en": "Rudrabhishekam Muhurtham",
      "te": "రుద్రాభిషేకం ముహూర్తం (Rudrabhishekam)",
      "hi": "रुद्राभिषेक मुहूर्त (Rudrabhishekam)"
    },
    "short_desc": {
      "en": "Auspicious timings for sacred Shiva Linga abhishekam with Sri Rudram chantings.",
      "te": "శివలింగానికి ఏకాదశ రుద్రాభిషేకం, లఘురుద్ర పూజల శుభ ముహూర్తాలు.",
      "hi": "श्री रुद्राभिषेक, महामृत्युंजय एवं शिव आराधना का पावन मुहूर्त।"
    },
    "form_schema": "rudrabhishekam",
    "content": {
      "about": {
        "en": "Rudrabhishekam is the powerful Vedic bath ceremony of Lord Shiva with panchamrita, milk, and sacred waters accompanied by the recitation of Sri Rudram from the Krishna Yajurveda.",
        "te": "పరమశివునికి పంచామృతాలతో, శ్రీరుద్ర మంత్రాలతో జరిపే పవిత్ర అభిషేకం రుద్రాభిషేకం.",
        "hi": "यजुर्वेद के श्रीरुद्रम् पाठ के साथ शिवलिंग पर पंचामृत एवं गंगाजल से अभिषेक किया जाता है।"
      },
      "religious_significance": {
        "en": "Pacifies Lord Shiva in his fierce Rudra form to bestow peace, rain, health, and enlightenment.",
        "te": "ఈ పూజతో మహాశివుడు ప్రసన్నుడై ఆయురారోగ్యాలు, మోక్షాన్ని ప్రసాదిస్తాడు.",
        "hi": "भगवान भोलेनाथ की कृपा से अकाल मृत्यु, रोग और भय का नाश होता है।"
      },
      "why_muhurtham": {
        "en": "Dissolves deep karmic sins, cures chronic diseases, and restores mental bliss.",
        "te": "పూర్వజన్మ పాపాలు నశించి కుటుంబంలో శాంతి వెల్లివిరియడానికి ఉపకరిస్తుంది.",
        "hi": "समस्त पापों का शमन एवं मनवांछित फल प्राप्ति हेतु शुभ समय आवश्यक है।"
      },
      "selection_principles": {
        "en": "Mondays, Pradosham days, Masa Shivaratri, and Ardra/Pushya nakshatras.",
        "te": "సోమవారం, ప్రదోష కాలం, మాస శివరాత్రి, ఆరుద్ర నక్షత్రం అత్యంత ప్రశస్తం.",
        "hi": "सोमवार, प्रदोष काल एवं मासिक शिवरात्रि पर रुद्राभिषेक सर्वोत्तम है।"
      },
      "required_details": {
        "en": "Devotee name, abhishekam type, Shiva temple or home venue, and date range.",
        "te": "భక్తుని పేరు, అభిషేకం రకం, ఆలయం, తేదీల పరిధి.",
        "hi": "भक्त का नाम, अभिषेक का प्रकार, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "satyanarayana-puja": {
    "slug": "satyanarayana-puja",
    "legacy_slug": "satyanarayana-puja",
    "icon": "🍌",
    "category": "puja_religious",
    "title": {
      "en": "Satyanarayana Swamy Puja Muhurtham",
      "te": "సత్యనారాయణ స్వామి వ్రతం ముహూర్తం (Satyanarayana Puja)",
      "hi": "सत्यनारायण व्रत एवं कथा मुहूर्त (Satyanarayana Puja)"
    },
    "short_desc": {
      "en": "Auspicious timing for the sacred Sri Satyanarayana Vratam and Katha rituals.",
      "te": "సత్యనారాయణ స్వామి వ్రతం, కథ నిర్వహించడానికి శ్రేష్ఠమైన శుభ ముహూర్తాలు.",
      "hi": "भगवान श्री सत्यनारायण पूजन एवं कथा का पावन मुहूर्त।"
    },
    "form_schema": "satyanarayana_puja",
    "content": {
      "about": {
        "en": "Sri Satyanarayana Vrata is performed to thank the Lord upon achieving success, during house warmings, weddings, or on Poornima days for family well-being.",
        "te": "కోరిన కోరికలు నెరవేరినప్పుడు, శుభకార్యాల సందర్భంలో సత్యనారాయణ స్వామి వ్రతాన్ని భక్తితో చేస్తారు.",
        "hi": "सत्यनारायण व्रत परिवार में सुख, शांति, समृद्धि और संकल्प सिद्धि हेतु किया जाता है।"
      },
      "religious_significance": {
        "en": "Narrated by Lord Narayana to Sage Narada in Skanda Purana, guaranteeing fulfillment of righteousness.",
        "te": "స్కాంద పురాణోక్తమైన ఈ వ్రతంతో లక్ష్మీనారాయణుల కృప లభించి సకల సంపదలు కలుగుతాయి.",
        "hi": "स्कंद पुराण के अनुसार यह व्रत कलिकाल में समस्त कष्टों से मुक्ति दिलाता है।"
      },
      "why_muhurtham": {
        "en": "Ensures the Katha recitation concludes peacefully and sacred prasadam is shared joyfully.",
        "te": "వ్రతం నిర్విఘ్నంగా పూర్తయి సమస్త శుభాలు కలగడానికి ముహూర్తం ముఖ్యం.",
        "hi": "कथा एवं व्रत का पूर्ण फल प्राप्त करने हेतु शुभ समय ग्राह्य है।"
      },
      "selection_principles": {
        "en": "Poornima (Full Moon), Sankranti, and Ekadashi in evening sandhya or morning hours.",
        "te": "పూర్ణిమ, ఏకాదశి, సంక్రాంతి దినాలు మరియు గురువారాలు ఎంతో శ్రేష్ఠం.",
        "hi": "पूर्णिमा, एकादशी एवं गुरुवार को गोधूलि वेला में व्रत श्रेष्ठ है।"
      },
      "required_details": {
        "en": "Host name, occasion, venue city, and date range.",
        "te": "యజమాని పేరు, సందర్భం, నగరం, తేదీల పరిధి.",
        "hi": "यजमान का नाम, अवसर, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "temple-kumbhabhishekam": {
    "slug": "temple-kumbhabhishekam",
    "legacy_slug": "temple-ceremony",
    "icon": "🛕",
    "category": "puja_religious",
    "title": {
      "en": "Temple Consecration / Kumbhabhishekam Muhurtham",
      "te": "కుంభాభిషేకం ముహూర్తం (Kumbhabhishekam)",
      "hi": "कुंभाभिषेक / मंदिर प्रतिष्ठा मुहूर्त (Kumbhabhishekam)"
    },
    "short_desc": {
      "en": "Auspicious timings for Maha Kumbhabhishekam, temple Vimana kalasha abhishekam.",
      "te": "దేవాలయ విమాన గోపురంపై కలశ స్థాపన, మహా కుంభాభిషేక శుభ ముహూర్తాలు.",
      "hi": "मंदिर शिखर कलश स्थापना एवं महाकुंभाभिषेक का पावन मुहूर्त।"
    },
    "form_schema": "temple_kumbhabhishekam",
    "content": {
      "about": {
        "en": "Maha Kumbhabhishekam is the grand temple consecration ritual performed once every 12 years to re-infuse cosmic vitality into the sanctum sanctorum and Vimana gopurams.",
        "te": "ప్రతి 12 సంవత్సరాలకు ఒకసారి దేవాలయ గోపుర కలశాలపై పవిత్ర జలాలతో చేసే మహా సంప్రోక్షణ కుంభాభిషేకం.",
        "hi": "मंदिर के शिखर कलशों पर पवित्र जल से अभिषेक कर देव शक्ति को पुनः जाग्रत करने का महामहोत्सव।"
      },
      "religious_significance": {
        "en": "Agama Shastra prescribes multi-day Yagasala pujas culminating in pouring holy waters over golden Kalashas.",
        "te": "ఆగమ శాస్త్రోక్తంగా యాగశాలలో పూజించిన కలశ జలాలతో విగ్రహాలకు దివ్యశక్తి చేకూరుతుంది.",
        "hi": "आगम शास्त्र के अनुसार यज्ञाग्नि से अभिमंत्रित जल से देव विग्रह प्रतिष्ठित होते हैं।"
      },
      "why_muhurtham": {
        "en": "Locks the spiritual aura into the stone and metals for future centuries, showering peace upon the region.",
        "te": "ఆలయం నుండి తరతరాలు దివ్య ప్రకంపనలు వెలువడటానికి స్థిర లగ్నం తప్పనిసరి.",
        "hi": "मंदिर में युगों-युगों तक देव चैतन्य विद्यमान रहे, इसलिए यह मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Strict Sthira Lagna in Uttarayana. Pure Brahma Muhurtham and Guru aspects.",
        "te": "ఉత్తరాయణం, స్థిర లగ్నం, బ్రహ్మ ముహూర్తం తప్పనిసరి.",
        "hi": "उत्तरायण, स्थिर लग्न एवं ब्रह्म मुहूर्त में ही कुंभाभिषेक किया जाता है।"
      },
      "required_details": {
        "en": "Temple trust name, temple name, deity, location, and date range.",
        "te": "ట్రస్ట్ పేరు, ఆలయం పేరు, దేవతా నామం, ప్రదేశం, తేదీల పరిధి.",
        "hi": "मंदिर ट्रस्ट का नाम, देवता, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "deity-pratishtha": {
    "slug": "deity-pratishtha",
    "legacy_slug": "deity-pratishtha",
    "icon": "🗿",
    "category": "puja_religious",
    "title": {
      "en": "Deity Installation / Pratishtha Muhurtham",
      "te": "విగ్రహ ప్రతిష్ఠ ముహూర్తం (Prana Pratishtha)",
      "hi": "मूर्ति प्राण प्रतिष्ठा मुहूर्त (Deity Pratishtha)"
    },
    "short_desc": {
      "en": "Sacred Prana Pratishtha idol installation and sanctification auspicious timings.",
      "te": "దేవతా విగ్రహాల ప్రాణప్రతిష్ఠ మరియు గర్భగుడి స్థాపన శుభ ముహూర్తాలు.",
      "hi": "देव प्रतिमा की प्राण प्रतिष्ठा एवं गर्भगृह स्थापना का शुभ मुहूर्त।"
    },
    "form_schema": "deity_pratishtha",
    "content": {
      "about": {
        "en": "Prana Pratishtha is the sacred ritual wherein the living cosmic divinity is awakened and established within stone or panchaloha murthis using Vedic mantras.",
        "te": "శిలా లేదా పంచలోహ విగ్రహాలలో ప్రాణాన్ని ప్రతిష్టించి సాక్షాత్తు దైవత్వాన్ని ఆవాహన చేసే సంస్కారం.",
        "hi": "मंत्रोच्चार द्वारा प्रतिमा में देव प्राणों का संचार कर उसे पूजनीय बनाने का संस्कार।"
      },
      "religious_significance": {
        "en": "Transforms sculpted matter into a living portal of divine communion and grace.",
        "te": "శిల్పానికి దైవకళ ఉట్టిపడి భక్తుల కోరికలు తీర్చే శక్తిని ప్రసాదిస్తుంది.",
        "hi": "प्रतिमा चैतन्यमयी होकर भक्तों की प्रार्थना सुनने में समर्थ होती है।"
      },
      "why_muhurtham": {
        "en": "Any astrological flaw during Pratishtha impacts the temple's prosperity and town safety.",
        "te": "ప్రతిష్ఠా లగ్నం దోషరహితంగా ఉంటేనే ఊరికి, భక్తులకు క్షేమం కలుగుతుంది.",
        "hi": "नगर एवं यजमान के कल्याण हेतु शुद्ध स्थिर लग्न का चयन अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Sthira Lagna with benefic planets in Kendra and Trikona houses.",
        "te": "కేంద్ర, త్రికోణాలలో శుభగ్రహాలు ఉండే స్థిర లగ్నాలు శ్రేష్ఠం.",
        "hi": "केंद्र और त्रिकोण में शुभ ग्रहों से युक्त स्थिर लग्न ग्राह्य है।"
      },
      "required_details": {
        "en": "Mandir organizer name, deity name, temple location, and date range.",
        "te": "నిర్వాహకుని పేరు, విగ్రహ నామం, ప్రాంతం, తేదీల పరిధి.",
        "hi": "आयोजक का नाम, मूर्ति का नाम, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "yajna-homa": {
    "slug": "yajna-homa",
    "legacy_slug": "puja-homa",
    "icon": "🔥",
    "category": "puja_religious",
    "title": {
      "en": "Yajna / Homa Commencement Muhurtham",
      "te": "యాగం / హోమం ప్రారంభ ముహూర్తం (Yajna & Homa)",
      "hi": "यज्ञ / हवन आरंभ मुहूर्त (Yajna & Homa)"
    },
    "short_desc": {
      "en": "Auspicious timing for Chandi Homam, Maha Rudra Yajna, and grand Vedic sacrifices.",
      "te": "చండీ హోమం, మహారుద్ర యాగాలు, శాంతి హోమాలు ప్రారంభించే శుభ ముహూర్తాలు.",
      "hi": "चंडी होम, अतिरुद्र यज्ञ एवं विशिष्ट हवनों के शुभारंभ का शुभ मुहूर्त।"
    },
    "form_schema": "yajna_homa",
    "content": {
      "about": {
        "en": "Commencing grand Vedic Yajnas and homams in an auspicious Muhurtham directs holy oblations directly to cosmic devatas, generating maximum spiritual merit.",
        "te": "శుభ సమయంలో చేసే హోమం దేవతలకు ప్రీతికరమై సంపూర్ణ పుణ్యఫలాన్ని, లోక కళ్యాణాన్ని ఇస్తుంది.",
        "hi": "शुभ मुहूर्त में किया गया यज्ञ देवताओं को तृप्त कर अभीष्ट फल प्रदान करता है।"
      },
      "religious_significance": {
        "en": "Agni Deva acts as the pure divine conduit carrying Ahutis to the higher heavens.",
        "te": "అగ్నిదేవుని ముఖతః దేవతలకు హవిస్సులు చేరి వర్షాలు, శాంతి కలుగుతాయి.",
        "hi": "अग्नि देव आहुतियों को सीधे देवताओं तक पहुंचाते हैं।"
      },
      "why_muhurtham": {
        "en": "Agni Vasam (fire residing on earth) must be verified to ensure the sacrifice yields boons.",
        "te": "అగ్ని నివాసం పృథ్విపై ఉన్నప్పుడే హోమం చేయాలని శాస్త్ర నియమం.",
        "hi": "अग्नि वास का विचार हवन हेतु परम आवश्यक है।"
      },
      "selection_principles": {
        "en": "Shukla Paksha with Agni Vasam aligned to Prithvi (Earth).",
        "te": "పృథ్వీ అగ్ని నివాసం, శుక్ల పక్షం శ్రేష్ఠమైనవి.",
        "hi": "पृथ्वी पर अग्नि वास होने पर ही यज्ञ आरंभ करना चाहिए।"
      },
      "required_details": {
        "en": "Yajamana name, Yajna type, sacrificial arena venue, and date range.",
        "te": "యజమాని పేరు, యాగం రకం, వేదిక, తేదీల పరిధి.",
        "hi": "यजमान का नाम, यज्ञ का नाम, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "travel": {
    "slug": "travel",
    "legacy_slug": "travel",
    "icon": "✈️",
    "category": "travel_education",
    "title": {
      "en": "Travel / Journey Muhurtham",
      "te": "ప్రయాణ ముహూర్తం (Travel / Shubh Yatra)",
      "hi": "शुभ यात्रा मुहूर्त (Travel / Journey)"
    },
    "short_desc": {
      "en": "Auspicious timing for general travel, business journeys, and family trips.",
      "te": "సుదూర ప్రయాణాలు, వ్యాపార యాత్రలకు శుభ ముహూర్తాలు.",
      "hi": "सुखद एवं सुरक्षित यात्रा हेतु शास्त्रोक्त शुभ मुहूर्त।"
    },
    "form_schema": "travel",
    "content": {
      "about": {
        "en": "Departing for a journey in a favorable Muhurtham ensures travel safety, pleasant experiences, and successful mission completion.",
        "te": "శుభ సమయంలో బయలుదేరితే ప్రయాణం సురక్షితంగా, అనుకున్న పనులు విజయవంతంగా పూర్తవుతాయి.",
        "hi": "शुभ काल में प्रस्थान करने से यात्रा सुखद और उद्देश्य सफल होता है।"
      },
      "religious_significance": {
        "en": "Invokes Lord Hanuman and Ashwini Kumaras for accident-free voyages and safety.",
        "te": "హనుమంతుని స్మరిస్తూ ప్రయాణం ప్రారంభిస్తే సర్వ రక్ష కలుగుతుంది.",
        "hi": "हनुमान जी की कृपा से यात्रा में समस्त विघ्नों का शमन होता है।"
      },
      "why_muhurtham": {
        "en": "Disha Shoola (prohibited directional travel on certain weekdays) must be strictly avoided.",
        "te": "దిశా శూల మరియు వర్జ్యం లేని శుభ సమయంలో ప్రయాణించాలి.",
        "hi": "दिशा शूल और राहुकाल का त्याग यात्रा में परम आवश्यक है।"
      },
      "selection_principles": {
        "en": "Avoid traveling in the direction of Disha Shoola. Chara (movable) Lagna is favored.",
        "te": "చర లగ్నం, దిశా శూల లేని దిశ ప్రయాణానికి అనుకూలం.",
        "hi": "यात्रा की दिशा के अनुसार दिशा शूल का विचार किया जाता है।"
      },
      "required_details": {
        "en": "Traveler name, origin city, destination, departure date range, and preferred hours.",
        "te": "ప్రయాణికుడి పేరు, ప్రారంభ స్థలం, గమ్యం, తేదీల పరిధి.",
        "hi": "यात्री का नाम, प्रस्थान स्थल, गंतव्य एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        5,
        7,
        8,
        13,
        17,
        22,
        23,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        6,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30,
        1
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "foreign-travel": {
    "slug": "foreign-travel",
    "legacy_slug": "foreign-travel",
    "icon": "🌐",
    "category": "travel_education",
    "title": {
      "en": "Foreign Travel Muhurtham",
      "te": "విదేశీ ప్రయాణ ముహూర్తం (Foreign Travel)",
      "hi": "विदेश यात्रा मुहूर्त (Foreign Travel)"
    },
    "short_desc": {
      "en": "Auspicious timing for overseas travel, international flights, and relocation.",
      "te": "విదేశాలకు వెళ్లడానికి, అంతర్జాతీయ విమాన ప్రయాణాలకు శుభ ముహూర్తాలు.",
      "hi": "विदेश गमन, अंतरराष्ट्रीय उड़ान एवं विदेश प्रवास का शुभ मुहूर्त।"
    },
    "form_schema": "foreign_travel",
    "content": {
      "about": {
        "en": "Crossing seas and traveling to foreign lands in an auspicious hora ensures smooth visa clearances, safe flights, and prosperous settlement abroad.",
        "te": "విదేశాలకు వెళ్లేటప్పుడు సరైన ముహూర్తం చూస్తే ఇమిగ్రేషన్ సమస్యలు రాకుండా ప్రయాణం సాఫీగా సాగుతుంది.",
        "hi": "विदेश यात्रा के शुभारंभ का शुभ मुहूर्त यात्रा को सफल और फलदायी बनाता है।"
      },
      "religious_significance": {
        "en": "Propitiates Varuna Deva (ocean and sky deity) and Lord Ganesha.",
        "te": "వరుణ దేవుని, గణపతిని ప్రార్థించి సముద్ర, ఆకాశ ప్రయాణాలు చేస్తారు.",
        "hi": "वरुण देव और गणेश जी की कृपा से परदेश में सुरक्षा और सफलता मिलती है।"
      },
      "why_muhurtham": {
        "en": "Prevents flight cancellations, lost baggage, and immigration deportations.",
        "te": "విమాన ప్రయాణాలలో ఆటంకాలు రాకుండా రక్షణ కలుగుతుంది.",
        "hi": "यात्रा में विघ्न और वीजा सम्बन्धी अड़चनों से बचाव होता है।"
      },
      "selection_principles": {
        "en": "Movable (Chara) signs like Aries, Cancer, Libra, Capricorn with Moon in favorable houses.",
        "te": "చర లగ్నాలు, శుభ చంద్రబలం విదేశీ ప్రయాణాలకు శ్రేష్ఠం.",
        "hi": "चर लग्न और चंद्रबल विदेश यात्रा हेतु अनुकूल हैं।"
      },
      "required_details": {
        "en": "Traveler name, country destination, origin airport city, and date range.",
        "te": "ప్రయాణికుడి పేరు, వెళ్లే దేశం, విమానాశ్రయం నగరం, తేదీల పరిధి.",
        "hi": "यात्री का नाम, देश, हवाई अड्डा एवं दिनांक সীমা।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "pilgrimage-departure": {
    "slug": "pilgrimage-departure",
    "legacy_slug": "pilgrimage-departure",
    "icon": "🛕",
    "category": "travel_education",
    "title": {
      "en": "Pilgrimage Departure Muhurtham",
      "te": "తీర్థయాత్ర ప్రయాణ ముహూర్తం (Pilgrimage Departure)",
      "hi": "तीर्थ यात्रा प्रस्थान मुहूर्त (Pilgrimage Departure)"
    },
    "short_desc": {
      "en": "Auspicious timings for embarking on sacred Teertha Yatras, Char Dham, and temple visits.",
      "te": "చార్ ధామ్, కాశీ, రామేశ్వరం వంటి తీర్థయాత్రలకు బయలుదేరే శుభ ముహూర్తాలు.",
      "hi": "चार धाम, काशी, रामेश्वरम आदि पावन तीर्थ यात्रा प्रस्थान का शुभ मुहूर्त।"
    },
    "form_schema": "pilgrimage_departure",
    "content": {
      "about": {
        "en": "Departing for sacred pilgrimages under an auspicious star ensures physical stamina, safe passage through holy shrines, and maximum spiritual fruit.",
        "te": "పుణ్యక్షేత్రాలకు వెళ్లేటప్పుడు శాస్త్రోక్త సమయంలో బయలుదేరితే యాత్ర ఫలప్రదమవుతుంది.",
        "hi": "तीर्थ यात्रा पर प्रस्थान शुभ समय में करने से यात्रा सुखद और पुण्य फलदायी होती है।"
      },
      "religious_significance": {
        "en": "The pilgrim takes a sacred vow (Sankalpa) before the household deity before setting foot outward.",
        "te": "ఇంటి దైవానికి కొబ్బరికాయ కొట్టి తీర్థయాత్ర సంకల్పం తీసుకుంటారు.",
        "hi": "गृह देवता की आज्ञा लेकर संकल्पपूर्वक यात्रा आरंभ की जाती है।"
      },
      "why_muhurtham": {
        "en": "Guards against mountain landslides, pilgrim health exhaustion, and accidents.",
        "te": "కొండ ప్రాంతాల్లో ప్రమాదాలు జరగకుండా యాత్ర నిర్విఘ్నంగా సాగుతుంది.",
        "hi": "कठिन मार्गों पर सुरक्षा एवं देव दर्शन की सुलभता हेतु मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Pushya, Shravana, Ashwini, and Hasta nakshatras in Shukla Paksha.",
        "te": "పుష్యమి, శ్రవణం, హస్త నక్షత్రాలు మరియు శుక్ల పక్షం అనుకూలం.",
        "hi": "पुष्य एवं श्रवण नक्षत्र में तीर्थ गमन सर्वोत्तम माना गया है।"
      },
      "required_details": {
        "en": "Pilgrim name, holy Kshetra destination, starting city, and date range.",
        "te": "యాత్రికుడి పేరు, క్షేత్రం, బయలుదేరే నగరం, తేదీల పరిధి.",
        "hi": "तीर्थयात्री का नाम, गंतव्य, प्रस्थान स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "start-education": {
    "slug": "start-education",
    "legacy_slug": "start-education",
    "icon": "🎓",
    "category": "travel_education",
    "title": {
      "en": "Start Education Muhurtham",
      "te": "విద్యాభ్యాస ప్రారంభ ముహూర్తం (Start Education)",
      "hi": "विद्यारंभ एवं उच्च शिक्षा मुहूर्त (Start Education)"
    },
    "short_desc": {
      "en": "Auspicious timing for school admissions, college joining, and commencing new academic years.",
      "te": "పాఠశాల, కళాశాలల్లో ప్రవేశం పొందడానికి, ఉన్నత విద్య ప్రారంభించడానికి ముహూర్తాలు.",
      "hi": "विद्यालय प्रवेश, उच्च शिक्षा एवं नवीन सत्र प्रारंभ का शुभ मुहूर्त।"
    },
    "form_schema": "start_education",
    "content": {
      "about": {
        "en": "Entering school, university, or starting a new academic year under favorable planetary aspects instills intellectual sharpness and academic success.",
        "te": "కళాశాలలో చేరే రోజు మంచి ముహూర్తం ఉంటే విద్యాభ్యాసం నిరాటంకంగా సాగి డిగ్రీలు లభిస్తాయి.",
        "hi": "शुभ समय में विद्यालय अथवा विश्वविद्यालय में प्रवेश से ज्ञान और मेधा की वृद्धि होती है।"
      },
      "religious_significance": {
        "en": "Invokes Goddess Saraswati and Brihaspati, supreme preceptors of wisdom.",
        "te": "సరస్వతీ దేవి, బృహస్పతి అనుగ్రహంతో పరీక్షల్లో ఉత్తమ ర్యాంకులు వస్తాయి.",
        "hi": "मां सरस्वती एवं गुरु की कृपा से उच्च शिक्षा में सफलता मिलती है।"
      },
      "why_muhurtham": {
        "en": "Prevents academic backlogs, exam stress, and disciplinary issues.",
        "te": "చదువులో ఏకాగ్రత లోపించకుండా, ఉత్తమ ఫలితాలు సాధించడానికి ముహూర్తం ముఖ్యం.",
        "hi": "परीक्षा में उत्तम परिणाम और एकाग्रता हेतु मुहूर्त अनिवार्य है।"
      },
      "selection_principles": {
        "en": "Wednesday and Thursday with Mercury or Jupiter in strong positions.",
        "te": "బుధ, గురువారాలు, రోహిణి, హస్త, అనురాధ నక్షత్రాలు అనుకూలం.",
        "hi": "बुधवार एवं गुरुवार को बुध-गुरु के प्रभाव में प्रवेश लें।"
      },
      "required_details": {
        "en": "Student name, academic level/course, institution city, and date range.",
        "te": "విద్యార్థి పేరు, కోర్సు, కళాశాల నగరం, తేదీల పరిధి.",
        "hi": "छात्र का नाम, पाठ्यक्रम, संस्थान स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "start-course": {
    "slug": "start-course",
    "legacy_slug": "start-course",
    "icon": "💻",
    "category": "travel_education",
    "title": {
      "en": "Start a Course Muhurtham",
      "te": "కోర్సు ప్రారంభ ముహూర్తం (Start a Course)",
      "hi": "प्रशिक्षण एवं कोर्स आरंभ मुहूर्त (Start a Course)"
    },
    "short_desc": {
      "en": "Auspicious timings for commencing vocational courses, certifications, and skills training.",
      "te": "సాఫ్ట్‌వేర్ కోర్సులు, వృత్తి విద్యా శిక్షణ ప్రారంభించడానికి శుభ ముహూర్తాలు.",
      "hi": "कौशल प्रशिक्षण, प्रोफेशनल कोर्स एवं सर्टिफिकेशन आरंभ का शुभ मुहूर्त।"
    },
    "form_schema": "start_course",
    "content": {
      "about": {
        "en": "Beginning a new certification or professional upskilling program in an auspicious hour ensures mastery over the subject and immediate career placement.",
        "te": "నూతన నైపుణ్యాలు నేర్చుకోవడానికి సరైన సమయంలో ప్రారంభిస్తే ఉద్యోగావకాశాలు మెరుగవుతాయి.",
        "hi": "व्यावसायिक प्रशिक्षण शुभ काल में प्रारंभ करने से विषय में निपुणता और आजीविका प्राप्त होती है।"
      },
      "religious_significance": {
        "en": "Lord Hayagriva bestows speed of comprehension and memory retention.",
        "te": "హయగ్రీవ స్వామి అనుగ్రహంతో జ్ఞాపకశక్తి, గ్రహణ శక్తి పెరుగుతాయి.",
        "hi": "भगवान हयग्रीव की कृपा से स्मरण शक्ति और एकाग्रता बढ़ती है।"
      },
      "why_muhurtham": {
        "en": "Prevents abandoning the course midway due to loss of motivation or difficulty.",
        "te": "కోర్సు మధ్యలో ఆగిపోకుండా విజయవంతంగా పూర్తి చేయడానికి సహాయపడుతుంది.",
        "hi": "पाठ्यक्रम बीच में न छूटे और सफलता मिले, इसलिए मुहूर्त देखा जाता है।"
      },
      "selection_principles": {
        "en": "Mercury and Sun horas on benefic weekdays in Shukla Paksha.",
        "te": "బుధ, రవి హోరలు మరియు శుక్ల పక్షం శ్రేష్ఠం.",
        "hi": "बुध एवं सूर्य की शुभ होरा में कोर्स आरंभ करें।"
      },
      "required_details": {
        "en": "Learner name, course title, location, and date range.",
        "te": "అభ్యర్థి పేరు, కోర్సు పేరు, ప్రదేశం, తేదీల పరిధి.",
        "hi": "शिक्षार्थी का नाम, कोर्स का नाम एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "farming-sowing": {
    "slug": "farming-sowing",
    "legacy_slug": "farming-sowing",
    "icon": "🌾",
    "category": "travel_education",
    "title": {
      "en": "Farming / Sowing Muhurtham",
      "te": "వ్యవసాయ విత్తనాల విత్తే ముహూర్తం (Farming & Sowing)",
      "hi": "कृषि एवं बीज बोने का मुहूर्त (Farming & Sowing)"
    },
    "short_desc": {
      "en": "Auspicious timing for sowing crops, ploughing fields, and agricultural harvests.",
      "te": "పొలం దున్నడానికి, విత్తనాలు నాటడానికి, పంట కోతలకు శ్రేష్ఠమైన ముహూర్తాలు.",
      "hi": "खेत जोतने, बीज बोने एवं फसल कटाई का शास्त्रोक्त शुभ मुहूर्त।"
    },
    "form_schema": "farming_sowing",
    "content": {
      "about": {
        "en": "Ploughing the land and sowing the first seeds under auspicious lunar nakshatras guarantees healthy germination, abundant crop yields, and protection from pests.",
        "te": "పొలంలో మొదటి విత్తనం నాటే సమయం శాస్త్రోక్తంగా ఉంటే పంట సమృద్ధిగా పండి రైతుకు లాభాలు వస్తాయి.",
        "hi": "खेत में प्रथम बीज शुभ नक्षत्र में बोने से फसल लहलहाती है और कीट-पतंगों से रक्षा होती है।"
      },
      "religious_significance": {
        "en": "Honors Mother Bhudevi, Lord Indra (rain deity), and Lord Balarama (wielder of the plough).",
        "te": "భూమాత, ఇంద్రుడు, బలరామ భగవానుని అనుగ్రహంతో సకాలంలో వర్షాలు కురుస్తాయి.",
        "hi": "भूदेवी एवं इंद्र देव की कृपा से समय पर वर्षा और उत्तम पैदावार होती है।"
      },
      "why_muhurtham": {
        "en": "Shields against drought, crop diseases, unseasonal hailstorms, and market crashes.",
        "te": "పంటకు చీడపీడలు పట్టకుండా, లాభసాటి దిగుబడి రావడానికి ముహూర్తం ముఖ్యం.",
        "hi": "अकाल, ओलावृष्टि एवं रोगों से फसल की रक्षा हेतु मुहूर्त आवश्यक है।"
      },
      "selection_principles": {
        "en": "Fixed and gentle nakshatras: Rohini, Mrigashira, Uttara Phalguni, Hasta, Anuradha, and Shravana.",
        "te": "రోహిణి, మృగశిర, హస్త, అనురాధ, శ్రవణం నక్షత్రాలు వ్యవసాయానికి శ్రేష్ఠం.",
        "hi": "रोहिणी, मृगशिरा, हस्त एवं अनुराधा नक्षत्र कृषि हेतु उत्तम हैं।"
      },
      "required_details": {
        "en": "Farmer name, crop type, field location, and date range.",
        "te": "రైతు పేరు, పంట రకం, పొలం ప్రదేశం, తేదీల పరిధి.",
        "hi": "किसान का नाम, फसल का प्रकार, स्थल एवं दिनांक।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  },
  "general-auspicious": {
    "slug": "general-auspicious",
    "legacy_slug": "general-auspicious",
    "icon": "✨",
    "category": "travel_education",
    "title": {
      "en": "General Auspicious-Time Finder",
      "te": "సర్వ కార్య శుభ ముహూర్త దర్శిని (General Auspicious Finder)",
      "hi": "सर्वकार्य सिद्धि शुभ मुहूर्त (General Auspicious Finder)"
    },
    "short_desc": {
      "en": "Universal Vedic auspicious timing finder for any sacred task or noble undertaking.",
      "te": "ఏదైనా మంచి పని, సంకల్పం ప్రారంభించడానికి సర్వతోముఖ శుభ ముహూర్తాలు.",
      "hi": "किसी भी शुभ कार्य के शुभारंभ हेतु सर्वमान्य शुभ मुहूर्त दर्शक।"
    },
    "form_schema": "general_karya",
    "content": {
      "about": {
        "en": "When embarking on any general noble deed, purchase, or personal milestone, DJV calculates universal Shubh Muhurtham windows free of all classical doshas.",
        "te": "ఏదైనా మంచి పని ప్రారంభించడానికి రాహుకాలం, యమగండం, దుర్ముహూర్తం లేని పవిత్ర సమయాలను గణిస్తుంది.",
        "hi": "किसी भी मांगलिक कार्य, संकल्प अथवा नवीन प्रयास हेतु दोषरहित शुभ काल का निर्धारण।"
      },
      "religious_significance": {
        "en": "Invokes Sri Mahavishnu: \"Shubham Karoti Kalyanam\", aligning cosmic hours with divine grace.",
        "te": "భగవత్ సంకల్పంతో చేసే ఏ కార్యమైనా నిర్విఘ్నంగా పూర్తవుతుంది.",
        "hi": "भगवान नारायण के स्मरण से समस्त कार्य सिद्ध होते हैं।"
      },
      "why_muhurtham": {
        "en": "Ensures whatever you start blossoms into prosperity, fulfillment, and peace of mind.",
        "te": "ప్రారంభించిన పని విజయవంతమై మనశ్శాంతి కలగడానికి ముహూర్తం తోడ్పడుతుంది.",
        "hi": "आरंभ किया गया कार्य चिरस्थायी सुख और सफलता प्रदान करता है।"
      },
      "selection_principles": {
        "en": "Benefic tithis, auspicious nakshatras, and pristine daytime Abhijit/Amrit slots.",
        "te": "శుభ తిథులు, అనుకూల నక్షత్రాలు, అభిజిత్ ముహూర్త కాలం.",
        "hi": "शुभ तिथि, नक्षत्र एवं अभिजित मुहूर्त काल का चयन किया जाता है।"
      },
      "required_details": {
        "en": "Primary devotee name, specific intention/purpose, location, and date range.",
        "te": "వ్యక్తి పేరు, సంకల్ప ఉద్దేశం, ప్రాంతం, తేదీల పరిధి.",
        "hi": "नाम, कार्य का उद्देश्य, स्थल एवं दिनांक सीमा।"
      }
    },
    "faqs": [],
    "rules": {
      "favorable_nakshatras": [
        1,
        4,
        5,
        8,
        12,
        13,
        14,
        15,
        17,
        21,
        26,
        27
      ],
      "prohibited_nakshatras": [
        2,
        3,
        9,
        18
      ],
      "favorable_tithis": [
        2,
        3,
        5,
        7,
        10,
        11,
        12,
        13
      ],
      "prohibited_tithis": [
        4,
        9,
        14,
        30
      ],
      "favorable_weekdays": [
        1,
        3,
        4,
        5,
        0
      ],
      "prohibited_weekdays": [
        2,
        6
      ],
      "prohibited_yogas": [
        "Vyatipata",
        "Vaidhriti"
      ],
      "prohibited_karanas": [
        "Vishti",
        "Bhadra"
      ]
    },
    "related_poojas": [
      "ganapathi-homam"
    ],
    "related_mantras": [
      "gayatri-mantra"
    ],
    "related_festivals": []
  }
};

  const CATEGORIES_DATA = {
    'marriage_relationships': {
      slug: 'marriage_relationships',
      icon: '💍',
      title: { en: 'Marriage & Relationships', te: 'వివాహం & సంబంధాలు', hi: 'विवाह एवं संबंध' }
    },
    'home_property': {
      slug: 'home_property',
      icon: '🏠',
      title: { en: 'Home, Property & Construction', te: 'గృహం, స్థలం & నిర్మాణం', hi: 'गृह, संपत्ति एवं निर्माण' }
    },
    'baby_family': {
      slug: 'baby_family',
      icon: '👶',
      title: { en: 'Baby & Family Samskaras', te: 'శిశు సంస్కారాలు & కుటుంబ పర్వాలు', hi: 'शिशु संस्कार एवं पारिवारिक पर्व' }
    },
    'vehicles_purchases': {
      slug: 'vehicles_purchases',
      icon: '🚗',
      title: { en: 'Vehicles & Purchases', te: 'వాహనాలు & కొనుగోళ్లు', hi: 'वाहन एवं क्रय-विक्रय' }
    },
    'business_career': {
      slug: 'business_career',
      icon: '💼',
      title: { en: 'Business, Career & Finance', te: 'వ్యాపారం, ఉద్యోగం & ఆర్థికం', hi: 'व्यापार, आजीविका एवं वित्त' }
    },
    'puja_religious': {
      slug: 'puja_religious',
      icon: '🔥',
      title: { en: 'Puja & Religious Activities', te: 'పూజ & ధార్మిక కార్యక్రమాలు', hi: 'पूजा एवं धार्मिक अनुष्ठान' }
    },
    'travel_education': {
      slug: 'travel_education',
      icon: '✈️',
      title: { en: 'Travel, Education & Other', te: 'ప్రయాణం, విద్య & ఇతరాలు', hi: 'यात्रा, विद्या एवं अन्य' }
    }
  };

  /**
   * Tarabala calculation helper.
   */
  function calculateTarabala(birthNak, transitNak) {
    var diff = (transitNak - birthNak);
    if (diff < 0) diff += 27;
    var taraNum = (diff % 9) + 1;
    var names = {
      1: 'Janma', 2: 'Sampat', 3: 'Vipat', 4: 'Kshema',
      5: 'Pratyak', 6: 'Sadhana', 7: 'Naidhana', 8: 'Mitra', 9: 'Parama Mitra'
    };
    var isAuspicious = [2, 4, 6, 8, 9].indexOf(taraNum) !== -1;
    var isCritical = (taraNum === 7);
    return {
      number: taraNum,
      name: names[taraNum] || 'Unknown',
      isAuspicious: isAuspicious,
      isCriticalAvoid: isCritical
    };
  }

  return {
    EVENTS_DATA: EVENTS_DATA,
    CATEGORIES_DATA: CATEGORIES_DATA,
    calculateTarabala: calculateTarabala,

    getEvent: function (slug) {
      if (EVENTS_DATA[slug]) return EVENTS_DATA[slug];
      // Search by legacy slug
      for (var k in EVENTS_DATA) {
        if (EVENTS_DATA[k].legacy_slug === slug) return EVENTS_DATA[k];
      }
      return null;
    },

    getAllEvents: function () {
      return Object.values(EVENTS_DATA);
    },

    getEventsByCategory: function (catSlug) {
      return Object.values(EVENTS_DATA).filter(function (e) {
        return e.category === catSlug;
      });
    },

    getCategories: function () {
      return Object.values(CATEGORIES_DATA);
    }
  };
});
