"""Extracts the translatable strings of the theme and the plugin and builds their Greek translations.

Run:  python3 wordpress/tools/build_translations.py
Theme  (text domain vassilisdesign):          vassilisdesign/languages/vassilisdesign.pot, el.po, el.mo
Plugin (text domain vassilisdesign-elements): vassilisdesign-elements/languages/vassilisdesign-elements.pot,
                                               vassilisdesign-elements-el.po, vassilisdesign-elements-el.mo
Strings without a Greek entry below stay in English.
"""
import ast
import pathlib
import re
import struct

ROOT = pathlib.Path(__file__).resolve().parent.parent
PACKAGES = [
    (ROOT / 'vassilisdesign', 'vassilisdesign', 'el'),
    (ROOT / 'vassilisdesign-elements', 'vassilisdesign-elements', 'vassilisdesign-elements-el'),
]

EL = {
    # Front end
    'Skip to content': 'Μετάβαση στο περιεχόμενο',
    'Main': 'Κύριο μενού',
    'Work': 'Έργα',
    'Studio': 'Στούντιο',
    'Contact': 'Επικοινωνία',
    'Legal': 'Νομικά',
    'Cookie settings': 'Ρυθμίσεις cookies',
    'Back to top': 'Επιστροφή στην κορυφή',
    'All work': 'Όλα τα έργα',
    'All': 'Όλα',
    'Sector': 'Τομέας',
    'Sectors': 'Τομείς',
    'Services': 'Υπηρεσίες',
    'Location': 'Τοποθεσία',
    'Project': 'Έργο',
    'Scope': 'Εύρος εργασιών',
    'About the project': 'Για το έργο',
    'Next project': 'Επόμενο έργο',
    'Works': 'Έργα',
    'No Works yet.': 'Δεν υπάρχουν ακόμη έργα.',
    'Greece': 'Ελλάδα',
    'Independent design studio\nGreece': 'Ανεξάρτητο στούντιο σχεδιασμού\nΕλλάδα',
    'Viber and WhatsApp available': 'Διαθέσιμο σε Viber και WhatsApp',
    # Works admin
    'Work': 'Έργο',
    'Add Work': 'Προσθήκη έργου',
    'Add new Work': 'Προσθήκη νέου έργου',
    'Edit Work': 'Επεξεργασία έργου',
    'New Work': 'Νέο έργο',
    'View Work': 'Προβολή έργου',
    'View Works': 'Προβολή έργων',
    'Search Works': 'Αναζήτηση έργων',
    'No Works found.': 'Δεν βρέθηκαν έργα.',
    'No Works found in Trash.': 'Δεν βρέθηκαν έργα στα διαγραμμένα.',
    'All Works': 'Όλα τα έργα',
    'Cover image': 'Εικόνα εξωφύλλου',
    'Set cover image': 'Ορισμός εικόνας εξωφύλλου',
    'Remove cover image': 'Αφαίρεση εικόνας εξωφύλλου',
    'Use as cover image': 'Χρήση ως εικόνα εξωφύλλου',
    'Work published.': 'Το έργο δημοσιεύτηκε.',
    'Work updated.': 'Το έργο ενημερώθηκε.',
    'Add new Sector': 'Προσθήκη νέου τομέα',
    'Edit Sector': 'Επεξεργασία τομέα',
    'All Sectors': 'Όλοι οι τομείς',
    'Work details': 'Στοιχεία έργου',
    'Scope (one item per line)': 'Εύρος εργασιών (ένα ανά γραμμή)',
    'Gallery': 'Γκαλερί',
    'Cover display': 'Εμφάνιση εξωφύλλου',
    'Cover': 'Εξώφυλλο',
    'Choose images': 'Επιλογή εικόνων',
    'Remove all': 'Αφαίρεση όλων',
    'Work gallery': 'Γκαλερί έργου',
    'Use these images': 'Χρήση αυτών των εικόνων',
    'Photo or screenshot: fill the frame': 'Φωτογραφία ή στιγμιότυπο: γέμισμα πλαισίου',
    'Packshot or logo on white: fit inside the frame': 'Προϊόν ή λογότυπο σε λευκό: προσαρμογή στο πλαίσιο',
    'One item per line. Shown in the red box on the Work page.': 'Ένα στοιχείο ανά γραμμή. Εμφανίζεται στο κόκκινο πλαίσιο της σελίδας του έργου.',
    'The thinking before the design: what was explored and the idea it led to. Shown in the red box on the Work page.': 'Η σκέψη πριν από τον σχεδιασμό: τι εξετάστηκε και σε ποια ιδέα οδήγησε. Εμφανίζεται στο κόκκινο πλαίσιο της σελίδας του έργου.',
    'The research and the idea behind the design, in one or two sentences.': 'Η έρευνα και η ιδέα πίσω από τον σχεδιασμό, σε μία ή δύο προτάσεις.',
    'Live website': 'Ιστότοπος',
    'Optional. Adds a "Visit website" link under the scope, opening in a new tab.': 'Προαιρετικό. Προσθέτει σύνδεσμο «Επισκεφθείτε τον ιστότοπο» κάτω από το εύρος εργασιών, σε νέα καρτέλα.',
    'Visit website': 'Επισκεφθείτε τον ιστότοπο',
    'On phones the whole image is shown.': 'Στα κινητά εμφανίζεται ολόκληρη η εικόνα.',
    'On phones each image is shown whole.': 'Στα κινητά κάθε εικόνα εμφανίζεται ολόκληρη.',
    'Sector / Service on hover': 'Τομέας / Υπηρεσία στο πέρασμα',
    'Filter labels (Sectors, Services)': 'Ετικέτες φίλτρων (Τομείς, Υπηρεσίες)',
    'Filter buttons': 'Κουμπιά φίλτρων',
    'Filter button: current': 'Κουμπί φίλτρου: επιλεγμένο',
    'Title, Sector and Service filters and all Works of the current archive. Use inside a Theme Builder "Archive" template for Works.': 'Τίτλος, φίλτρα Τομέων και Υπηρεσιών και όλα τα έργα του τρέχοντος αρχείου. Χρησιμοποιήστε το σε πρότυπο «Αρχείο» του Theme Builder για τα έργα.',
    'Service': 'Υπηρεσία',
    'Add new Service': 'Προσθήκη νέας υπηρεσίας',
    'Edit Service': 'Επεξεργασία υπηρεσίας',
    'All Services': 'Όλες οι υπηρεσίες',
    'Brand Identity': 'Εταιρική ταυτότητα',
    'Logo Design': 'Σχεδιασμός λογοτύπου',
    'Packaging': 'Συσκευασία',
    'Label Design': 'Σχεδιασμός ετικέτας',
    'Web Design': 'Σχεδιασμός ιστοσελίδων',
    'Print & Editorial': 'Έντυπα και εκδόσεις',
    'Stationery': 'Εταιρικά έντυπα',
    'Signage & Wayfinding': 'Σήμανση',
    'Art Direction': 'Καλλιτεχνική διεύθυνση',
    'Social Media': 'Μέσα κοινωνικής δικτύωσης',
    'Title = client or project name. Excerpt = the short summary shown on the Work page. Cover image = the tile on the homepage. Tick the Sector and Services in the boxes on the right.': 'Τίτλος = όνομα πελάτη ή έργου. Απόσπασμα = η σύντομη περίληψη στη σελίδα του έργου. Εικόνα εξωφύλλου = το πλακίδιο στην αρχική σελίδα. Επιλέξτε Τομέα και Υπηρεσίες στα πλαίσια δεξιά.',
    'View the full image': 'Προβολή ολόκληρης της εικόνας',
    'View image %1$d of %2$d full size': 'Προβολή εικόνας %1$d από %2$d σε πλήρες μέγεθος',
    'Image viewer': 'Προβολή εικόνων',
    'Close': 'Κλείσιμο',
    'Previous image': 'Προηγούμενη εικόνα',
    'Next image': 'Επόμενη εικόνα',
    'Open full image on click': 'Ολόκληρη εικόνα με κλικ',
    'Shape': 'Σχήμα',
    'Images per row': 'Εικόνες ανά σειρά',
    'Space between images': 'Απόσταση μεταξύ εικόνων',
    'Image shape': 'Σχήμα εικόνας',
    'As uploaded': 'Όπως ανέβηκε',
    'Fit': 'Προσαρμογή',
    'Fill the shape (may crop)': 'Γέμισμα σχήματος (μπορεί να κόψει)',
    'Show the whole image': 'Ολόκληρη η εικόνα',
    'Cover background': 'Φόντο εξωφύλλου',
    'Scope text': 'Κείμενο εύρους εργασιών',
    '"Visit website" link': 'Σύνδεσμος «Επισκεφθείτε τον ιστότοπο»',
    'Sector filter: current': 'Φίλτρο τομέα: επιλεγμένο',
    '(opens in a new tab)': '(ανοίγει σε νέα καρτέλα)',
    'Header menu': 'Μενού κεφαλίδας',
    'Footer legal menu': 'Μενού νομικών (υποσέλιδο)',
    'Studio details': 'Στοιχεία στούντιο',
    'Email': 'Email',
    'Phone': 'Τηλέφωνο',
    'Footer text': 'Κείμενο υποσέλιδου',
    'Messaging note': 'Σημείωση μηνυμάτων',
    'Clock label': 'Ετικέτα ρολογιού',
    'Wordmark, first line': 'Λογότυπο, πρώτη γραμμή',
    'Wordmark, second line': 'Λογότυπο, δεύτερη γραμμή',
    'No Works yet. Add them under Works → Add Work.': 'Δεν υπάρχουν ακόμη έργα. Προσθέστε τα από Έργα → Προσθήκη έργου.',
    'Legal details': 'Νομικά στοιχεία',
    'Legal name (person or company)': 'Επωνυμία (φυσικό πρόσωπο ή εταιρεία)',
    'Person responsible for content': 'Υπεύθυνος περιεχομένου',
    'Street address': 'Διεύθυνση',
    'Postcode': 'Ταχυδρομικός κώδικας',
    'City': 'Πόλη',
    'VAT number (ΑΦΜ)': 'ΑΦΜ',
    'Tax office (ΔΟΥ)': 'ΔΟΥ',
    'GEMI number (ΓΕΜΗ), or "Not registered"': 'Αριθμός ΓΕΜΗ, ή «Δεν απαιτείται»',
    'Courts of (city)': 'Αρμόδια δικαστήρια (πόλη)',
    'Hosting provider (name, country)': 'Πάροχος φιλοξενίας (όνομα, χώρα)',
    'Email provider': 'Πάροχος email',
    'Accountant or accounting software (optional)': 'Λογιστής ή λογισμικό (προαιρετικό)',
    'Server log retention (days)': 'Διατήρηση αρχείων καταγραφής (ημέρες)',
    'Works archive': 'Αρχείο έργων',
    'Layout': 'Διάταξη',
    'Works per row (desktop)': 'Έργα ανά σειρά (υπολογιστής)',
    'Full width (no side margins)': 'Πλήρες πλάτος (χωρίς πλευρικά περιθώρια)',
    'Title and details under each image': 'Τίτλος και στοιχεία κάτω από κάθε εικόνα',
    'Staggered heights': 'Κλιμακωτά ύψη',
    'Show': 'Εμφάνιση',
    'Fonts and colours': 'Γραμματοσειρές και χρώματα',
    'VD Hero image': 'VD Εικόνα hero',
    'Phones': 'Κινητά',
    'Display size': 'Μέγεθος προβολής',
    'Cover': 'Κάλυψη',
    'Contain': 'Προσαρμογή',
    'Auto': 'Αυτόματο',
    'Custom': 'Προσαρμοσμένο',
    'Width': 'Πλάτος',
    'Position': 'Θέση',
    'Repeat': 'Επανάληψη',
    'X Position': 'Θέση X',
    'Y Position': 'Θέση Y',
    'Logos per row': 'Λογότυπα ανά σειρά',
    'Space around each logo': 'Κενό γύρω από κάθε λογότυπο',
    'Larger space = smaller logos.': 'Μεγαλύτερο κενό = μικρότερα λογότυπα.',
    'Cell shape': 'Σχήμα κελιού',
    'Square (1:1)': 'Τετράγωνο (1:1)',
    'Wide (2:1)': 'Φαρδύ (2:1)',
    'Line thickness': 'Πάχος γραμμής',
    '0 hides the lines.': 'Το 0 κρύβει τις γραμμές.',
    'Line colour': 'Χρώμα γραμμών',
    'Top line colour': 'Χρώμα πάνω γραμμής',
    'Works on phones (0 = all)': 'Έργα στα κινητά (0 = όλα)',
    'Works per row on phones': 'Έργα ανά σειρά στα κινητά',
    'Equal heights on phones': 'Ίδιο ύψος στα κινητά',
    'Image': 'Εικόνα',
    'Image description': 'Περιγραφή εικόνας',
    'Height': 'Ύψος',
    'Image: space on the left': 'Εικόνα: κενό αριστερά',
    'Image: space on the right': 'Εικόνα: κενό δεξιά',
    'Red band: distance from top': 'Κόκκινη λωρίδα: απόσταση από πάνω',
    'Red band: height': 'Κόκκινη λωρίδα: ύψος',
    'Reveal animation': 'Κίνηση εμφάνισης',
    'Movement on scroll': 'Κίνηση στο scroll',
    'Image background': 'Φόντο εικόνας',
    'Arrow button': 'Κουμπί με βέλος',
    'Arrow button on hover': 'Κουμπί με βέλος στο hover',
    'Arrow': 'Βέλος',
    'Colour': 'Χρώμα',
    'Title': 'Τίτλος',
    'Title full stop': 'Τελεία τίτλου',
    'Work title': 'Τίτλος έργου',
    'Work title on hover': 'Τίτλος έργου στο hover',
    'Details under the title': 'Στοιχεία κάτω από τον τίτλο',
    'Text on hover': 'Κείμενο στο hover',
    'Red cover / red block': 'Κόκκινη επικάλυψη / κόκκινο μπλοκ',
    'Red box': 'Κόκκινο πλαίσιο',
    'Red band': 'Κόκκινη λωρίδα',
    'Wordmark': 'Λογότυπο',
    'Text': 'Κείμενο',
    'Lines': 'Γραμμές',
    'All Works (by Order)': 'Όλα τα έργα (κατά σειρά)',
    'Works in one Sector': 'Έργα ενός τομέα',
    'Works I choose': 'Έργα της επιλογής μου',
    'Choose a Sector': 'Επιλέξτε τομέα',
    'Works (in this order)': 'Έργα (με αυτή τη σειρά)',
    'Red cover with text on hover': 'Κόκκινη επικάλυψη με κείμενο στο hover',
    'Space between Works (px, empty = default)': 'Απόσταση μεταξύ έργων (px, κενό = προεπιλογή)',
    'To link a name, add | and the address after it, e.g. Porto Carras | https://portocarras.com': 'Για σύνδεσμο σε ένα όνομα, προσθέστε | και τη διεύθυνση μετά, π.χ. Porto Carras | https://portocarras.com',
    'Open links in a new tab': 'Άνοιγμα συνδέσμων σε νέα καρτέλα',
    'Linked name on hover': 'Όνομα με σύνδεσμο στο hover',
}
# 'Work' means "Work" (menu, plural sense) on the front end and "Work" (one item) in the admin:
# the front-end menu label wins, since visitors see it.
EL['Work'] = 'Έργα'

def call_re(domain):
    return re.compile(r"""(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*('(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*")\s*,\s*'""" + re.escape(domain) + "'")


def php_string(token):
    if token[0] == "'":
        return token[1:-1].replace("\\'", "'").replace('\\\\', '\\')
    return ast.literal_eval(token)  # double-quoted: \n etc. are real escapes


def extract(folder, domain):
    found = {}
    call = call_re(domain)
    for path in sorted(folder.rglob('*.php')):
        rel = path.relative_to(folder).as_posix()
        for i, line in enumerate(path.read_text(encoding='utf-8').splitlines(), 1):
            for m in call.finditer(line):
                found.setdefault(php_string(m.group(1)), []).append(f'{rel}:{i}')
    return found


def po_escape(s):
    return s.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n')


def write_po(path, strings, translations, header):
    out = [f'msgid ""\nmsgstr ""\n{header}']
    for msgid, refs in strings.items():
        out.append(f"#: {' '.join(refs[:3])}\nmsgid \"{po_escape(msgid)}\"\nmsgstr \"{po_escape(translations.get(msgid, ''))}\"\n")
    path.write_text('\n'.join(out), encoding='utf-8')


def write_mo(path, translations, header):
    entries = sorted([('', header)] + [(k, v) for k, v in translations.items()])
    ids = b''.join(k.encode() + b'\0' for k, _ in entries)
    strs = b''.join(v.encode() + b'\0' for _, v in entries)
    n = len(entries)
    keystart = 7 * 4 + 16 * n
    valuestart = keystart + len(ids)
    koffsets, voffsets, ko, vo = [], [], 0, 0
    for k, v in entries:
        koffsets += [len(k.encode()), keystart + ko]
        voffsets += [len(v.encode()), valuestart + vo]
        ko += len(k.encode()) + 1
        vo += len(v.encode()) + 1
    data = struct.pack('Iiiiiii', 0x950412de, 0, n, 7 * 4, 7 * 4 + n * 8, 0, 0)
    data += struct.pack(f'{len(koffsets)}i', *koffsets) + struct.pack(f'{len(voffsets)}i', *voffsets) + ids + strs
    path.write_bytes(data)


if __name__ == '__main__':
    used = set()
    for folder, domain, mo_name in PACKAGES:
        lang = folder / 'languages'
        lang.mkdir(exist_ok=True)
        strings = extract(folder, domain)
        base = f'"Project-Id-Version: {domain}\\n"\n"MIME-Version: 1.0\\n"\n"Content-Type: text/plain; charset=UTF-8\\n"\n"Content-Transfer-Encoding: 8bit\\n"\n"X-Domain: {domain}\\n"\n'
        write_po(lang / f'{domain}.pot', strings, {}, base)
        write_po(lang / f'{mo_name}.po', strings, EL, base + '"Language: el\\n"\n"Plural-Forms: nplurals=2; plural=(n != 1);\\n"\n')
        done = {k: v for k, v in EL.items() if k in strings}
        used |= set(done)
        write_mo(lang / f'{mo_name}.mo', done, f'Project-Id-Version: {domain}\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nLanguage: el\nPlural-Forms: nplurals=2; plural=(n != 1);\n')
        print(f'{domain}: {len(strings)} strings, {len(done)} translated to Greek')
    unused = [k for k in EL if k not in used]
    if unused:
        print('not used anywhere:', unused)
