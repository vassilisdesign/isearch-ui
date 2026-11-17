// All site copy lives here, in English (en) and Greek (el).
// Rendering is shared between the build (Node) and the browser, so keep this file free of DOM code.
//
// Project summaries are short, factual drafts based on the existing portfolio. Review and expand them
// before launch; they deliberately contain no results or figures that have not been confirmed.

export const studio = {
  name: 'VASSILISDESIGN',
  email: 'vassilisdesign@me.com',
  phone: '+30 694 769 0620',
  // Hero image: wine labels from the portfolio. Shown in black and white with the red band.
  heroPhoto: { src: 'images/hero-wine-labels.webp', w: 1848, h: 1738, fit: 'contain' }
};

const img = (src, w, h, fit = 'cover') => ({ src: `images/${src}.webp`, w, h, fit });

export const strings = {
  en: {
    'a11y.skip': 'Skip to content',
    'a11y.home': 'VASSILISDESIGN, home',
    'a11y.wordmark': 'VASSILISDESIGN, independent design studio',
    'nav.label': 'Main',
    'nav.work': 'Work',
    'nav.studio': 'Studio',
    'nav.contact': 'Contact',
    'lang.switch': 'Ελληνικά',
    'lang.short': 'ΕΛ',
    'clock.label': 'Greece',

    'meta.title': 'VASSILISDESIGN — Independent design studio',
    'meta.description': 'Independent design studio in Greece. Brand identities, packaging and labels, editorial and print, websites and digital communication for clients in Greece, the United States and the UK.',

    'hero.lead': 'Independent design studio.\nDistinctive identities.\nMeaningful design.',
    'hero.services': 'Brand identity\nPackaging and labels\nEditorial and print\nWeb design\nDigital communication',
    'hero.place': 'Based in Greece\nClients in Greece, the US and the UK',
    'hero.vertical': 'Independent design studio\nGreece and the United States',
    'hero.cta': 'See selected work',
    'hero.art': 'Wine labels designed by VASSILISDESIGN',

    'work.heading': 'Selected work',
    'work.intro': 'Identities, packaging, labels and websites for wineries, hotels, restaurants, food producers and professional firms.',

    'ids.heading': 'Identities',
    'ids.intro': 'Logos and identity systems built on typography and proportion.',

    'services.heading': 'Identity to every touchpoint',
    'services.intro': 'We take a brand from its first mark to its complete physical and digital presence.',
    'service.1.title': 'Brand identity',
    'service.1.text': 'Logos and complete visual identity systems that stay recognisable wherever they appear.',
    'service.2.title': 'Packaging and labels',
    'service.2.text': 'Labels and packaging for wine, food, beverages and consumer products, prepared for print down to colour separation.',
    'service.3.title': 'Editorial and print',
    'service.3.text': 'Corporate publications, brochures, catalogues and advertising, designed with the press in mind.',
    'service.4.title': 'Web design',
    'service.4.text': 'Websites designed and built in-house, from WordPress development to hosting.',
    'service.5.title': 'Digital communication',
    'service.5.text': 'Social media creative and campaigns that carry the identity into everyday communication.',

    'studio.heading': 'Studio',
    'principles.title': 'What we hold to',
    'principles.list': 'Typography first\nDetail and proportion\nBuilt for production\nDesign and technology',
    'industries.title': 'Industries',
    'industries.list': 'Wine\nHospitality and tourism\nFood and beverage\nProfessional services',
    'studio.p1': 'VASSILISDESIGN is an independent design studio in Greece, working with clients in Greece, the United States and the UK.',
    'studio.p2': 'We let typography and composition carry the message, and we adapt the approach to each brand, product and audience instead of repeating one style.',
    'studio.p3': 'Hands-on experience in print, packaging production and colour separation, together with in-house web development, means every idea is designed to be made.',

    'clients.heading': 'Clients',
    'clients.intro': 'A selection of the businesses we have worked with.',
    'sector.wine': 'Wine and packaging',
    'sector.hosp': 'Hospitality and tourism',
    'sector.food': 'Restaurants and food',
    'sector.corp': 'Corporate and professional services',

    'contact.heading': 'Start a project',
    'contact.lead': 'Tell us about your brand, product or website. We reply to every enquiry.',
    'contact.or': 'Or write to us directly',
    'contact.messaging': 'Viber and WhatsApp available',
    'form.name': 'Name',
    'form.email': 'Email',
    'form.company': 'Company (optional)',
    'form.budget': 'Budget',
    'form.budget.0': 'Not sure yet',
    'form.budget.1': 'Under €5,000',
    'form.budget.2': '€5,000–15,000',
    'form.budget.3': '€15,000–30,000',
    'form.budget.4': 'Over €30,000',
    'form.message': 'About the project',
    'form.message.hint': 'What you need, roughly when, and anything we should see first.',
    'form.submit': 'Send message',
    'form.sending': 'Sending…',
    'form.sent': 'Message sent. We will be in touch soon.',
    'form.failed': 'The message did not send. Check your connection and try again, or email vassilisdesign@me.com.',
    'form.mailto': 'Your email app is opening with the message filled in. Press send there to reach us.',
    'form.err.name': 'Add your name.',
    'form.err.email': 'Enter an email address like name@company.com.',
    'form.err.message': 'Tell us a little about the project.',

    'footer.base': 'Independent design studio\nGreece',
    'footer.rights': '© 2026 VASSILISDESIGN',
    'footer.top': 'Back to top',

    'case.client': 'Client',
    'case.services': 'Services',
    'case.sector': 'Sector',
    'case.location': 'Location',
    'case.scope': 'Scope',
    'case.about': 'About the project',
    'case.next': 'Next project',
    'case.all': 'All work',
    'case.index': 'Project'
  },

  el: {
    'a11y.skip': 'Μετάβαση στο περιεχόμενο',
    'a11y.home': 'VASSILISDESIGN, αρχική',
    'a11y.wordmark': 'VASSILISDESIGN, ανεξάρτητο στούντιο σχεδιασμού',
    'nav.label': 'Κύριο μενού',
    'nav.work': 'Έργα',
    'nav.studio': 'Στούντιο',
    'nav.contact': 'Επικοινωνία',
    'lang.switch': 'English',
    'lang.short': 'EN',
    'clock.label': 'Ελλάδα',

    'meta.title': 'VASSILISDESIGN — Ανεξάρτητο στούντιο σχεδιασμού',
    'meta.description': 'Ανεξάρτητο στούντιο σχεδιασμού στην Ελλάδα. Εταιρικές ταυτότητες, συσκευασίες και ετικέτες, έντυπα, ιστοσελίδες και ψηφιακή επικοινωνία για πελάτες σε Ελλάδα, ΗΠΑ και Ηνωμένο Βασίλειο.',

    'hero.lead': 'Ανεξάρτητο στούντιο σχεδιασμού.\nΞεχωριστές ταυτότητες.\nΣχεδιασμός με νόημα.',
    'hero.services': 'Εταιρική ταυτότητα\nΣυσκευασία και ετικέτες\nΈντυπα και εκδόσεις\nΣχεδιασμός ιστοσελίδων\nΨηφιακή επικοινωνία',
    'hero.place': 'Με έδρα την Ελλάδα\nΠελάτες σε Ελλάδα, ΗΠΑ και Ηνωμένο Βασίλειο',
    'hero.vertical': 'Ανεξάρτητο στούντιο σχεδιασμού\nΕλλάδα και Ηνωμένες Πολιτείες',
    'hero.cta': 'Δείτε επιλεγμένα έργα',
    'hero.art': 'Ετικέτες κρασιού από το VASSILISDESIGN',

    'work.heading': 'Επιλεγμένα έργα',
    'work.intro': 'Ταυτότητες, συσκευασίες, ετικέτες και ιστοσελίδες για οινοποιεία, ξενοδοχεία, εστιατόρια, παραγωγούς τροφίμων και επαγγελματίες.',

    'ids.heading': 'Ταυτότητες',
    'ids.intro': 'Λογότυπα και συστήματα ταυτότητας, χτισμένα πάνω στην τυπογραφία και τις αναλογίες.',

    'services.heading': 'Από την ταυτότητα παντού',
    'services.intro': 'Συνοδεύουμε ένα brand από το πρώτο του σήμα μέχρι την πλήρη φυσική και ψηφιακή του παρουσία.',
    'service.1.title': 'Εταιρική ταυτότητα',
    'service.1.text': 'Λογότυπα και ολοκληρωμένα συστήματα οπτικής ταυτότητας που αναγνωρίζονται όπου κι αν εμφανίζονται.',
    'service.2.title': 'Συσκευασία και ετικέτες',
    'service.2.text': 'Ετικέτες και συσκευασίες για κρασί, τρόφιμα, ποτά και καταναλωτικά προϊόντα, έτοιμες για εκτύπωση μέχρι τον διαχωρισμό χρωμάτων.',
    'service.3.title': 'Έντυπα και εκδόσεις',
    'service.3.text': 'Εταιρικές εκδόσεις, μπροσούρες, κατάλογοι και διαφημίσεις, σχεδιασμένα με γνώση της εκτύπωσης.',
    'service.4.title': 'Σχεδιασμός ιστοσελίδων',
    'service.4.text': 'Ιστοσελίδες που σχεδιάζουμε και υλοποιούμε εμείς, από την ανάπτυξη σε WordPress μέχρι τη φιλοξενία.',
    'service.5.title': 'Ψηφιακή επικοινωνία',
    'service.5.text': 'Δημιουργικό για social media και καμπάνιες που μεταφέρουν την ταυτότητα στην καθημερινή επικοινωνία.',

    'studio.heading': 'Στούντιο',
    'principles.title': 'Οι αρχές μας',
    'principles.list': 'Πρώτα η τυπογραφία\nΛεπτομέρεια και αναλογίες\nΈτοιμο για παραγωγή\nΣχεδιασμός και τεχνολογία',
    'industries.title': 'Κλάδοι',
    'industries.list': 'Κρασί\nΦιλοξενία και τουρισμός\nΤρόφιμα και ποτά\nΕπαγγελματικές υπηρεσίες',
    'studio.p1': 'Το VASSILISDESIGN είναι ανεξάρτητο στούντιο σχεδιασμού στην Ελλάδα, με πελάτες σε Ελλάδα, Ηνωμένες Πολιτείες και Ηνωμένο Βασίλειο.',
    'studio.p2': 'Αφήνουμε την τυπογραφία και τη σύνθεση να μεταφέρουν το μήνυμα, και προσαρμόζουμε την προσέγγιση σε κάθε brand, προϊόν και κοινό αντί να επαναλαμβάνουμε ένα ύφος.',
    'studio.p3': 'Η εμπειρία στην εκτύπωση, την παραγωγή συσκευασιών και τον διαχωρισμό χρωμάτων, μαζί με την ανάπτυξη ιστοσελίδων στο στούντιο, σημαίνει ότι κάθε ιδέα σχεδιάζεται για να υλοποιηθεί.',

    'clients.heading': 'Πελάτες',
    'clients.intro': 'Μια επιλογή από τις επιχειρήσεις με τις οποίες έχουμε συνεργαστεί.',
    'sector.wine': 'Κρασί και συσκευασία',
    'sector.hosp': 'Φιλοξενία και τουρισμός',
    'sector.food': 'Εστίαση και τρόφιμα',
    'sector.corp': 'Εταιρικές και επαγγελματικές υπηρεσίες',

    'contact.heading': 'Ξεκινήστε ένα έργο',
    'contact.lead': 'Πείτε μας για το brand, το προϊόν ή την ιστοσελίδα σας. Απαντάμε σε κάθε μήνυμα.',
    'contact.or': 'Ή γράψτε μας απευθείας',
    'contact.messaging': 'Διαθέσιμο σε Viber και WhatsApp',
    'form.name': 'Όνομα',
    'form.email': 'Email',
    'form.company': 'Εταιρεία (προαιρετικό)',
    'form.budget': 'Προϋπολογισμός',
    'form.budget.0': 'Δεν έχει οριστεί ακόμη',
    'form.budget.1': 'Έως 5.000 €',
    'form.budget.2': '5.000–15.000 €',
    'form.budget.3': '15.000–30.000 €',
    'form.budget.4': 'Πάνω από 30.000 €',
    'form.message': 'Για το έργο',
    'form.message.hint': 'Τι χρειάζεστε, περίπου πότε, και ό,τι πρέπει να δούμε πρώτα.',
    'form.submit': 'Αποστολή μηνύματος',
    'form.sending': 'Αποστολή…',
    'form.sent': 'Το μήνυμα στάλθηκε. Θα επικοινωνήσουμε σύντομα μαζί σας.',
    'form.failed': 'Το μήνυμα δεν στάλθηκε. Ελέγξτε τη σύνδεσή σας και δοκιμάστε ξανά, ή γράψτε στο vassilisdesign@me.com.',
    'form.mailto': 'Ανοίγει η εφαρμογή email με το μήνυμα συμπληρωμένο. Πατήστε αποστολή εκεί για να μας το στείλετε.',
    'form.err.name': 'Συμπληρώστε το όνομά σας.',
    'form.err.email': 'Γράψτε ένα email όπως name@company.com.',
    'form.err.message': 'Πείτε μας λίγα λόγια για το έργο.',

    'footer.base': 'Ανεξάρτητο στούντιο σχεδιασμού\nΕλλάδα',
    'footer.rights': '© 2026 VASSILISDESIGN',
    'footer.top': 'Επιστροφή στην κορυφή',

    'case.client': 'Πελάτης',
    'case.services': 'Υπηρεσίες',
    'case.sector': 'Τομέας',
    'case.location': 'Τοποθεσία',
    'case.scope': 'Εύρος εργασιών',
    'case.about': 'Για το έργο',
    'case.next': 'Επόμενο έργο',
    'case.all': 'Όλα τα έργα',
    'case.index': 'Έργο'
  }
};

// Featured case studies, in display order. `cover` is the tile image; `images` fill the case-study page.
// fit: 'contain' for packshots and logos on white (they blend into the page), 'cover' for screenshots.
export const projects = [
  {
    slug: 'cava-oinos',
    client: 'Cava Oinos',
    sector: 'wine',
    cover: img('cava-oinos-web-ny', 800, 459),
    images: [img('cava-oinos-logo', 800, 306, 'contain'), img('cava-oinos-web-ny', 800, 459), img('cava-oinos-web-la', 800, 459)],
    en: {
      location: 'New York and Los Angeles, USA',
      services: 'Logo, websites',
      summary: 'A wine retailer with stores in New York and Los Angeles. We designed the logo and a website for each store.',
      scope: ['Logo design', 'Website, New York', 'Website, Los Angeles']
    },
    el: {
      location: 'Νέα Υόρκη και Λος Άντζελες, ΗΠΑ',
      services: 'Λογότυπο, ιστοσελίδες',
      summary: 'Κατάστημα κρασιών με παρουσία στη Νέα Υόρκη και το Λος Άντζελες. Σχεδιάσαμε το λογότυπο και μια ιστοσελίδα για κάθε κατάστημα.',
      scope: ['Σχεδιασμός λογοτύπου', 'Ιστοσελίδα, Νέα Υόρκη', 'Ιστοσελίδα, Λος Άντζελες']
    }
  },
  {
    slug: 'family-food',
    client: 'Family Food',
    sector: 'food',
    cover: img('family-graviera', 1142, 1089, 'contain'),
    images: [
      img('family-graviera', 1142, 1089, 'contain'), img('family-happy-pizza', 914, 1160, 'contain'),
      img('family-gouda', 1269, 1448, 'contain'), img('family-mix', 1185, 1194, 'contain'),
      img('family-tortillas', 1142, 1119, 'contain'), img('family-talagani', 1069, 1070, 'contain'),
      img('family-web', 800, 459)
    ],
    en: {
      location: 'Thessaloniki, Greece',
      services: 'Packaging, website',
      summary: 'A food company in Thessaloniki. We designed packaging across its cheese and dairy range, and the company website.',
      scope: ['Packaging design', 'Product range system', 'Website']
    },
    el: {
      location: 'Θεσσαλονίκη, Ελλάδα',
      services: 'Συσκευασία, ιστοσελίδα',
      summary: 'Εταιρεία τροφίμων στη Θεσσαλονίκη. Σχεδιάσαμε συσκευασίες για τη σειρά τυριών και γαλακτοκομικών της και την εταιρική ιστοσελίδα.',
      scope: ['Σχεδιασμός συσκευασίας', 'Σύστημα για τη σειρά προϊόντων', 'Ιστοσελίδα']
    }
  },
  {
    slug: 'mediterranean-hotels',
    client: 'Mediterranean Hotels',
    sector: 'hosp',
    cover: img('mediterranean-logo', 1248, 1130, 'contain'),
    images: [img('mediterranean-logo', 1248, 1130, 'contain'), img('olympus-riviera-logo', 780, 282, 'contain'), img('mediterranean-web', 800, 459)],
    en: {
      location: 'Olympus Riviera and Mykonos, Greece',
      services: 'Identity, website',
      summary: 'A hotel group with properties on the Olympus Riviera and in Mykonos. We designed the identity and the website.',
      scope: ['Group identity', 'Olympus Riviera logo', 'Website']
    },
    el: {
      location: 'Ολυμπιακή Ριβιέρα και Μύκονος, Ελλάδα',
      services: 'Ταυτότητα, ιστοσελίδα',
      summary: 'Ξενοδοχειακός όμιλος με μονάδες στην Ολυμπιακή Ριβιέρα και τη Μύκονο. Σχεδιάσαμε την ταυτότητα και την ιστοσελίδα.',
      scope: ['Ταυτότητα ομίλου', 'Λογότυπο Olympus Riviera', 'Ιστοσελίδα']
    }
  },
  {
    slug: 'ammos-estiatorio',
    client: 'Ammos Estiatorio',
    sector: 'food',
    cover: img('ammos-web', 800, 459),
    images: [img('ammos-web', 800, 459)],
    en: {
      location: 'New York, USA',
      services: 'Website, creative direction, social media',
      summary: 'A Greek seafood restaurant in New York. We designed the website and handle the creative direction, photographic compositions and social media campaigns.',
      scope: ['Website', 'Creative direction', 'Photographic compositions', 'Social media and promotional campaigns']
    },
    el: {
      location: 'Νέα Υόρκη, ΗΠΑ',
      services: 'Ιστοσελίδα, δημιουργική διεύθυνση, social media',
      summary: 'Ελληνικό εστιατόριο θαλασσινών στη Νέα Υόρκη. Σχεδιάσαμε την ιστοσελίδα και έχουμε τη δημιουργική διεύθυνση, τις φωτογραφικές συνθέσεις και τις καμπάνιες στα social media.',
      scope: ['Ιστοσελίδα', 'Δημιουργική διεύθυνση', 'Φωτογραφικές συνθέσεις', 'Social media και προωθητικές καμπάνιες']
    }
  },
  {
    slug: 'wine-labels',
    client: { en: 'Wine labels', el: 'Ετικέτες κρασιού' },
    sector: 'wine',
    cover: img('wine-erroso', 1434, 1160, 'contain'),
    images: [img('wine-erroso', 1434, 1160, 'contain'), img('wine-mavrotragano', 314, 642, 'contain'), img('wine-grand', 273, 769, 'contain'), img('wine-lineup', 890, 351, 'contain')],
    en: {
      location: 'Greece',
      services: 'Label design, packaging',
      summary: 'Labels for Greek wineries, from illustration and typography to the finished bottle, prepared for print and production.',
      scope: ['Label design', 'Typography and illustration', 'Print-ready artwork and colour separation']
    },
    el: {
      location: 'Ελλάδα',
      services: 'Σχεδιασμός ετικέτας, συσκευασία',
      summary: 'Ετικέτες για ελληνικά οινοποιεία, από την εικονογράφηση και την τυπογραφία μέχρι την τελική φιάλη, έτοιμες για εκτύπωση και παραγωγή.',
      scope: ['Σχεδιασμός ετικέτας', 'Τυπογραφία και εικονογράφηση', 'Μακέτες για εκτύπωση και διαχωρισμός χρωμάτων']
    }
  },
  {
    slug: 'artis',
    client: 'Artis Capital',
    sector: 'corp',
    cover: img('artis-logo', 847, 582, 'contain'),
    images: [img('artis-logo', 847, 582, 'contain'), img('artis-capital-web', 800, 459), img('artis-trade-web', 800, 459)],
    en: {
      location: 'New York, USA and London, UK',
      services: 'Identity, websites',
      summary: 'Artis Capital in New York and its sister company Artis Trade House in London work in international commerce and trade. We designed the Artis Capital identity and websites for both companies.',
      scope: ['Artis Capital identity', 'Website, Artis Capital', 'Website, Artis Trade House']
    },
    el: {
      location: 'Νέα Υόρκη, ΗΠΑ και Λονδίνο, Ηνωμένο Βασίλειο',
      services: 'Ταυτότητα, ιστοσελίδες',
      summary: 'Η Artis Capital στη Νέα Υόρκη και η αδελφή εταιρεία Artis Trade House στο Λονδίνο δραστηριοποιούνται στο διεθνές εμπόριο. Σχεδιάσαμε την ταυτότητα της Artis Capital και τις ιστοσελίδες και των δύο εταιρειών.',
      scope: ['Ταυτότητα Artis Capital', 'Ιστοσελίδα Artis Capital', 'Ιστοσελίδα Artis Trade House']
    }
  },
  {
    slug: 'open-ways',
    client: 'Open Ways',
    sector: 'hosp',
    cover: img('openways-logo', 843, 485, 'contain'),
    images: [img('openways-logo', 843, 485, 'contain'), img('openways-web', 800, 459)],
    en: {
      location: 'Thessaloniki, Greece',
      services: 'Identity, website',
      summary: 'An events and travel company in Thessaloniki. We designed the identity and the website.',
      scope: ['Logo and identity', 'Website']
    },
    el: {
      location: 'Θεσσαλονίκη, Ελλάδα',
      services: 'Ταυτότητα, ιστοσελίδα',
      summary: 'Εταιρεία εκδηλώσεων και ταξιδιών στη Θεσσαλονίκη. Σχεδιάσαμε την ταυτότητα και την ιστοσελίδα.',
      scope: ['Λογότυπο και ταυτότητα', 'Ιστοσελίδα']
    }
  },
  {
    slug: 'felea-goods',
    client: 'Felea Goods',
    sector: 'food',
    cover: img('felea-web', 800, 459),
    images: [img('felea-web', 800, 459), img('felea-page', 274, 1600, 'contain')],
    en: {
      location: 'Heraklion, Crete',
      services: 'Website',
      summary: 'A Cretan food company based in Heraklion. We designed and built its website.',
      scope: ['Website design', 'Development']
    },
    el: {
      location: 'Ηράκλειο, Κρήτη',
      services: 'Ιστοσελίδα',
      summary: 'Κρητική εταιρεία τροφίμων με έδρα το Ηράκλειο. Σχεδιάσαμε και υλοποιήσαμε την ιστοσελίδα της.',
      scope: ['Σχεδιασμός ιστοσελίδας', 'Υλοποίηση']
    }
  }
];

// Logo wall: shown in black and white, in colour on hover.
export const identities = [
  { name: 'Hotel Atlas', ...img('logo-atlas', 838, 541, 'contain') },
  { name: 'Mediterranean Hotels', ...img('mediterranean-logo', 1248, 1130, 'contain') },
  { name: 'Artis Capital', ...img('artis-logo', 847, 582, 'contain') },
  { name: 'Open Ways', ...img('openways-logo', 843, 485, 'contain') },
  { name: 'Queens PMU', ...img('logo-queens', 798, 512, 'contain') },
  { name: 'Ermes Greek CBD', ...img('logo-ermes', 844, 596, 'contain') },
  { name: 'CBD Extraction', ...img('logo-cbd', 829, 687, 'contain') },
  { name: 'All Travel Services', ...img('logo-ats', 773, 473, 'contain') },
  { name: 'Wuka', ...img('logo-wuka', 742, 460, 'contain') },
  { name: 'Olympus Riviera', ...img('olympus-riviera-logo', 780, 282, 'contain') },
  { name: 'Cava Oinos', ...img('cava-oinos-logo', 800, 306, 'contain') },
  { name: 'Ammos Cafe', ...img('ammos-logo', 785, 214, 'contain') }
];

// Client index, grouped by sector.
export const clients = {
  wine: ['Cava Oinos', 'Porto Carras', 'Dougos Winery', 'Lantides Winery', 'Wine Art Estate', 'POTEUS', 'Kanthia', 'Kourtis Winery', 'Olympus Wines', 'Taste Greek Wines'],
  hosp: ['Three Peaks Elevated Stay', 'SimosMare', 'Olympos Beach', 'Hotel Atlas', 'Mediterranean Hotels', 'Pieria Hotels Association', 'Open Ways'],
  food: ['Ammos Estiatorio, New York', 'Burger Lab', 'Family Food', 'Felea Goods'],
  corp: ['FBFK Law', 'Artis Capital', 'Artis Trade House', 'WCWCA', 'Sokrates Furniture', 'Queens PMU', 'CBD Extraction', 'Mercedes Driving School']
};
