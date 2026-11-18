"""Builds the Elementor homepage templates (English and Greek) for the VASSILISDESIGN theme.

Run:  python3 wordpress/tools/build_templates.py
Writes wordpress/vassilisdesign/setup/home-en.json and home-el.json, importable via
Elementor -> Templates -> Saved Templates -> Import, and used by the theme's one-click setup.
"""
import hashlib
import json
import pathlib

OUT = pathlib.Path(__file__).resolve().parent.parent / 'vassilisdesign' / 'setup'

COPY = {
    'en': {
        'title': 'Home',
        'hero': {
            'lead': 'Independent design studio.\nDistinctive identities.\nMeaningful design.',
            'meta_a': 'Brand identity\nPackaging and labels\nEditorial and print\nWeb design\nDigital communication',
            'meta_b': 'Based in Greece\nClients in Greece, the US and the UK',
            'vertical': 'Independent design studio\nGreece and the United States',
            'label': 'VASSILISDESIGN, independent design studio',
            'image_alt': 'Wine labels designed by VASSILISDESIGN',
            'cta_label': 'See selected work',
        },
        'work': ('Identities, packaging, labels and websites for wineries, hotels, restaurants, food producers and professional firms.', 'Selected work', 'All work'),
        'ids': ('Logos and identity systems built on typography and proportion.', 'Identities'),
        'services': ('We take a brand from its first mark to its complete physical and digital presence.', 'Identity to every touchpoint', [
            ('Brand identity', 'Logos and complete visual identity systems that stay recognisable wherever they appear.'),
            ('Packaging and labels', 'Labels and packaging for wine, food, beverages and consumer products, prepared for print down to colour separation.'),
            ('Editorial and print', 'Corporate publications, brochures, catalogues and advertising, designed with the press in mind.'),
            ('Web design', 'Websites designed and built in-house, from WordPress development to hosting.'),
            ('Digital communication', 'Social media creative and campaigns that carry the identity into everyday communication.'),
        ]),
        'studio': {
            'title': 'Studio', 'red_title': 'What we hold to',
            'red_list': 'Typography first\nDetail and proportion\nBuilt for production\nDesign and technology',
            'side_title': 'Industries', 'side_list': 'Wine\nHospitality and tourism\nFood and beverage\nProfessional services',
            'text': ('<p>VASSILISDESIGN is an independent design studio in Greece, working with clients in Greece, the United States and the UK.</p>'
                     '<p>We let typography and composition carry the message, and we adapt the approach to each brand, product and audience instead of repeating one style.</p>'
                     '<p>Hands-on experience in print, packaging production and colour separation, together with in-house web development, means every idea is designed to be made.</p>'),
        },
        'clients': ('A selection of the businesses we have worked with.', 'Clients', [
            ('Wine and packaging', 'Cava Oinos\nPorto Carras\nDougos Winery\nLantides Winery\nWine Art Estate\nPOTEUS\nKanthia\nKourtis Winery\nOlympus Wines\nTaste Greek Wines'),
            ('Hospitality and tourism', 'Three Peaks Elevated Stay\nSimosMare\nOlympos Beach\nHotel Atlas\nMediterranean Hotels\nPieria Hotels Association\nOpen Ways'),
            ('Restaurants and food', 'Ammos Estiatorio, New York\nBurger Lab\nFamily Food\nFelea Goods'),
            ('Corporate and professional services', 'FBFK Law\nArtis Capital\nArtis Trade House\nWCWCA\nSokrates Furniture\nQueens PMU\nCBD Extraction\nMercedes Driving School'),
        ]),
        'contact': {
            'title': 'Start a project', 'lead': 'Tell us about your brand, product or website. We reply to every enquiry.', 'direct': 'Or write to us directly',
        },
        'form': {
            'name': 'Name', 'email': 'Email', 'company': 'Company (optional)', 'budget': 'Budget',
            'budgets': 'Not sure yet\nUnder €5,000\n€5,000–15,000\n€15,000–30,000\nOver €30,000',
            'message': 'About the project', 'message_ph': 'What you need, roughly when, and anything we should see first.',
            'consent': 'I agree that VASSILISDESIGN uses these details to reply to my enquiry, as described in the [privacy]Privacy Policy[/privacy].',
            'submit': 'Send message', 'subject': 'New enquiry from the website',
            'success': 'Message sent. We will be in touch soon.',
            'error': 'The message did not send. Please try again, or email us directly.',
            'required': 'This field is required.', 'invalid': 'Please check this field.',
        },
    },
    'el': {
        'title': 'Αρχική',
        'hero': {
            'lead': 'Ανεξάρτητο στούντιο σχεδιασμού.\nΞεχωριστές ταυτότητες.\nΣχεδιασμός με νόημα.',
            'meta_a': 'Εταιρική ταυτότητα\nΣυσκευασία και ετικέτες\nΈντυπα και εκδόσεις\nΣχεδιασμός ιστοσελίδων\nΨηφιακή επικοινωνία',
            'meta_b': 'Με έδρα την Ελλάδα\nΠελάτες σε Ελλάδα, ΗΠΑ και Ηνωμένο Βασίλειο',
            'vertical': 'Ανεξάρτητο στούντιο σχεδιασμού\nΕλλάδα και Ηνωμένες Πολιτείες',
            'label': 'VASSILISDESIGN, ανεξάρτητο στούντιο σχεδιασμού',
            'image_alt': 'Ετικέτες κρασιού από το VASSILISDESIGN',
            'cta_label': 'Δείτε επιλεγμένα έργα',
        },
        'work': ('Ταυτότητες, συσκευασίες, ετικέτες και ιστοσελίδες για οινοποιεία, ξενοδοχεία, εστιατόρια, παραγωγούς τροφίμων και επαγγελματίες.', 'Επιλεγμένα έργα', 'Όλα τα έργα'),
        'ids': ('Λογότυπα και συστήματα ταυτότητας, χτισμένα πάνω στην τυπογραφία και τις αναλογίες.', 'Ταυτότητες'),
        'services': ('Συνοδεύουμε ένα brand από το πρώτο του σήμα μέχρι την πλήρη φυσική και ψηφιακή του παρουσία.', 'Από την ταυτότητα παντού', [
            ('Εταιρική ταυτότητα', 'Λογότυπα και ολοκληρωμένα συστήματα οπτικής ταυτότητας που αναγνωρίζονται όπου κι αν εμφανίζονται.'),
            ('Συσκευασία και ετικέτες', 'Ετικέτες και συσκευασίες για κρασί, τρόφιμα, ποτά και καταναλωτικά προϊόντα, έτοιμες για εκτύπωση μέχρι τον διαχωρισμό χρωμάτων.'),
            ('Έντυπα και εκδόσεις', 'Εταιρικές εκδόσεις, μπροσούρες, κατάλογοι και διαφημίσεις, σχεδιασμένα με γνώση της εκτύπωσης.'),
            ('Σχεδιασμός ιστοσελίδων', 'Ιστοσελίδες που σχεδιάζουμε και υλοποιούμε εμείς, από την ανάπτυξη σε WordPress μέχρι τη φιλοξενία.'),
            ('Ψηφιακή επικοινωνία', 'Δημιουργικό για social media και καμπάνιες που μεταφέρουν την ταυτότητα στην καθημερινή επικοινωνία.'),
        ]),
        'studio': {
            'title': 'Στούντιο', 'red_title': 'Οι αρχές μας',
            'red_list': 'Πρώτα η τυπογραφία\nΛεπτομέρεια και αναλογίες\nΈτοιμο για παραγωγή\nΣχεδιασμός και τεχνολογία',
            'side_title': 'Κλάδοι', 'side_list': 'Κρασί\nΦιλοξενία και τουρισμός\nΤρόφιμα και ποτά\nΕπαγγελματικές υπηρεσίες',
            'text': ('<p>Το VASSILISDESIGN είναι ανεξάρτητο στούντιο σχεδιασμού στην Ελλάδα, με πελάτες σε Ελλάδα, Ηνωμένες Πολιτείες και Ηνωμένο Βασίλειο.</p>'
                     '<p>Αφήνουμε την τυπογραφία και τη σύνθεση να μεταφέρουν το μήνυμα, και προσαρμόζουμε την προσέγγιση σε κάθε brand, προϊόν και κοινό αντί να επαναλαμβάνουμε ένα ύφος.</p>'
                     '<p>Η εμπειρία στην εκτύπωση, την παραγωγή συσκευασιών και τον διαχωρισμό χρωμάτων, μαζί με την ανάπτυξη ιστοσελίδων στο στούντιο, σημαίνει ότι κάθε ιδέα σχεδιάζεται για να υλοποιηθεί.</p>'),
        },
        'clients': ('Μια επιλογή από τις επιχειρήσεις με τις οποίες έχουμε συνεργαστεί.', 'Πελάτες', [
            ('Κρασί και συσκευασία', 'Cava Oinos\nPorto Carras\nDougos Winery\nLantides Winery\nWine Art Estate\nPOTEUS\nKanthia\nKourtis Winery\nOlympus Wines\nTaste Greek Wines'),
            ('Φιλοξενία και τουρισμός', 'Three Peaks Elevated Stay\nSimosMare\nOlympos Beach\nHotel Atlas\nMediterranean Hotels\nPieria Hotels Association\nOpen Ways'),
            ('Εστίαση και τρόφιμα', 'Ammos Estiatorio, New York\nBurger Lab\nFamily Food\nFelea Goods'),
            ('Εταιρικές και επαγγελματικές υπηρεσίες', 'FBFK Law\nArtis Capital\nArtis Trade House\nWCWCA\nSokrates Furniture\nQueens PMU\nCBD Extraction\nMercedes Driving School'),
        ]),
        'contact': {
            'title': 'Ξεκινήστε ένα έργο', 'lead': 'Πείτε μας για το brand, το προϊόν ή την ιστοσελίδα σας. Απαντάμε σε κάθε μήνυμα.', 'direct': 'Ή γράψτε μας απευθείας',
        },
        'form': {
            'name': 'Όνομα', 'email': 'Email', 'company': 'Εταιρεία (προαιρετικό)', 'budget': 'Προϋπολογισμός',
            'budgets': 'Δεν έχει οριστεί ακόμη\nΈως 5.000 €\n5.000–15.000 €\n15.000–30.000 €\nΠάνω από 30.000 €',
            'message': 'Για το έργο', 'message_ph': 'Τι χρειάζεστε, περίπου πότε, και ό,τι πρέπει να δούμε πρώτα.',
            'consent': 'Συμφωνώ να χρησιμοποιηθούν τα στοιχεία μου από το VASSILISDESIGN για την απάντηση στο αίτημά μου, όπως περιγράφεται στην [privacy]Πολιτική Απορρήτου[/privacy].',
            'submit': 'Αποστολή μηνύματος', 'subject': 'Νέο μήνυμα από την ιστοσελίδα',
            'success': 'Το μήνυμα στάλθηκε. Θα επικοινωνήσουμε σύντομα μαζί σας.',
            'error': 'Το μήνυμα δεν στάλθηκε. Δοκιμάστε ξανά ή γράψτε μας απευθείας.',
            'required': 'Το πεδίο είναι υποχρεωτικό.', 'invalid': 'Ελέγξτε αυτό το πεδίο.',
        },
    },
}


class Ids:
    """Deterministic 7-character element IDs, unique within a template."""

    def __init__(self, seed):
        self.seed, self.n = seed, 0

    def __call__(self):
        self.n += 1
        return hashlib.md5(f'{self.seed}{self.n}'.encode()).hexdigest()[:7]


def build(lang):
    c = COPY[lang]
    nid = Ids('vd-home-' + lang)

    def widget(kind, settings, classes=''):
        s = dict(settings)
        if classes:
            s['_css_classes'] = classes
        return {'id': nid(), 'elType': 'widget', 'widgetType': kind, 'settings': s, 'elements': [], 'isInner': False}

    def container(children, classes='vd-section', anchor='', title=''):
        s = {'content_width': 'full', 'flex_direction': 'column', 'css_classes': classes}
        if title:
            s['_title'] = title
        if anchor:
            s['_element_id'] = anchor
        return {'id': nid(), 'elType': 'container', 'settings': s, 'elements': children, 'isInner': False}

    def head(intro, title):
        return widget('vd-section-head', {'intro': intro, 'title': title, 'tag': 'h2'})

    def repeat(rows, keys):
        return [dict(zip(keys, row), _id=nid()) for row in rows]

    f = c['form']
    consent = f['consent'].replace('[privacy]', '<a href="{{privacy_url}}">').replace('[/privacy]', '</a>')
    fields = [
        {'custom_id': 'name', 'field_type': 'text', 'field_label': f['name'], 'required': 'true', 'width': '50', 'autocomplete': 'name'},
        {'custom_id': 'email', 'field_type': 'email', 'field_label': f['email'], 'required': 'true', 'width': '50'},
        {'custom_id': 'company', 'field_type': 'text', 'field_label': f['company'], 'width': '50'},
        {'custom_id': 'budget', 'field_type': 'select', 'field_label': f['budget'], 'field_options': f['budgets'], 'width': '50'},
        {'custom_id': 'message', 'field_type': 'textarea', 'field_label': f['message'], 'placeholder': f['message_ph'], 'required': 'true', 'rows': 5, 'width': '100'},
        {'custom_id': 'consent', 'field_type': 'acceptance', 'field_label': '', 'acceptance_text': consent, 'required': 'true', 'width': '100'},
        {'custom_id': 'website', 'field_type': 'honeypot', 'field_label': '', 'width': '100'},
    ]
    for field in fields:
        field['_id'] = nid()

    form = widget('form', {
        'form_name': 'Contact',
        'form_fields': fields,
        'show_labels': 'yes',
        'mark_required': '',
        'button_text': f['submit'],
        'button_width': '',
        'submit_actions': ['email'],
        'email_to': '{{email}}',
        'email_subject': f['subject'],
        'email_content': '[all-fields]',
        'email_from_name': 'VASSILISDESIGN',
        'email_reply_to': '[field id="email"]',
        'email_content_type': 'plain',
        'form_metadata': ['date', 'time', 'page_url'],
        'custom_messages': 'yes',
        'success_message': f['success'],
        'error_message': f['error'],
        'required_field_message': f['required'],
        'invalid_message': f['invalid'],
    }, 'vd-form')

    h = c['hero']
    content = [
        container([widget('vd-hero', {
            'line1': 'VASSILIS', 'line2': 'DESIGN', 'label': h['label'], 'lead': h['lead'],
            'meta_a': h['meta_a'], 'meta_b': h['meta_b'], 'vertical': h['vertical'], 'corner': 'GR / US',
            'image': {'id': '', 'url': ''}, 'image_alt': h['image_alt'], 'fit': 'contain',
            'cta': '#work', 'cta_label': h['cta_label'],
        })], classes='vd-bare', title='Hero'),
        container([head(*c['work'][:2]), widget('vd-works-grid', {'count': 10, 'sector': '', 'all_link': c['work'][2], 'columns': '5', 'full': 'yes', 'gap': 0, 'overlay': 'yes', 'captions': 'yes', 'staggered': ''})], anchor='work', title='Selected work'),
        container([head(*c['ids']), widget('vd-logo-wall', {'logos': []})], title='Identities'),
        container([head(*c['services'][:2]), widget('vd-numbered-list', {'items': repeat(c['services'][2], ('title', 'text'))})], title='Services'),
        container([widget('vd-studio', dict(c['studio'], image={'id': '', 'url': ''}))], anchor='studio', title='Studio'),
        container([head(*c['clients'][:2]), widget('vd-client-index', {'groups': repeat(c['clients'][2], ('title', 'names'))})], title='Clients'),
        container([widget('vd-contact-intro', c['contact']), form], classes='vd-section vd-section-end vd-contact', anchor='contact', title='Contact'),
    ]
    return {
        'title': f"VASSILISDESIGN {c['title']}",
        'type': 'page',
        'version': '0.4',
        'page_settings': {'template': 'elementor_header_footer'},
        'content': content,
    }


if __name__ == '__main__':
    OUT.mkdir(parents=True, exist_ok=True)
    for lang in COPY:
        path = OUT / f'home-{lang}.json'
        path.write_text(json.dumps(build(lang), ensure_ascii=False, indent=1) + '\n', encoding='utf-8')
        print('wrote', path)
