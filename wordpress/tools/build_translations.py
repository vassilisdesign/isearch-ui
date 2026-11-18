"""Extracts the theme's translatable strings and builds the Greek translation.

Run:  python3 wordpress/tools/build_translations.py
Writes languages/vassilisdesign.pot, languages/el.po and languages/el.mo in the theme folder.
Strings without a Greek entry below stay in English.
"""
import ast
import pathlib
import re
import struct

THEME = pathlib.Path(__file__).resolve().parent.parent / 'vassilisdesign'
LANG = THEME / 'languages'

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
    'Space between Works (px)': 'Απόσταση μεταξύ έργων (px)',
    'Red panel with text on hover': 'Κόκκινο πλαίσιο με κείμενο στο hover',
    'Title and details under each image': 'Τίτλος και στοιχεία κάτω από κάθε εικόνα',
    'Staggered heights': 'Κλιμακωτά ύψη',
    'Show': 'Εμφάνιση',
    'All Works (by Order)': 'Όλα τα έργα (κατά σειρά)',
    'Works in one Sector': 'Έργα ενός τομέα',
    'Works I choose': 'Έργα της επιλογής μου',
    'Choose a Sector': 'Επιλέξτε τομέα',
    'Works (in this order)': 'Έργα (με αυτή τη σειρά)',
    'Red cover with text on hover': 'Κόκκινη επικάλυψη με κείμενο στο hover',
    'Space between Works (px, empty = default)': 'Απόσταση μεταξύ έργων (px, κενό = προεπιλογή)',
}
# 'Work' means "Work" (menu, plural sense) on the front end and "Work" (one item) in the admin:
# the front-end menu label wins, since visitors see it.
EL['Work'] = 'Έργα'

CALL = re.compile(r"""(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*('(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*")\s*,\s*'vassilisdesign'""")


def php_string(token):
    if token[0] == "'":
        return token[1:-1].replace("\\'", "'").replace('\\\\', '\\')
    return ast.literal_eval(token)  # double-quoted: \n etc. are real escapes


def extract():
    found = {}
    for path in sorted(THEME.rglob('*.php')):
        rel = path.relative_to(THEME).as_posix()
        for i, line in enumerate(path.read_text(encoding='utf-8').splitlines(), 1):
            for m in CALL.finditer(line):
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
    LANG.mkdir(exist_ok=True)
    strings = extract()
    base = '"Project-Id-Version: VASSILISDESIGN 1.0.0\\n"\n"MIME-Version: 1.0\\n"\n"Content-Type: text/plain; charset=UTF-8\\n"\n"Content-Transfer-Encoding: 8bit\\n"\n"X-Domain: vassilisdesign\\n"\n'
    write_po(LANG / 'vassilisdesign.pot', strings, {}, base)
    write_po(LANG / 'el.po', strings, EL, base + '"Language: el\\n"\n"Plural-Forms: nplurals=2; plural=(n != 1);\\n"\n')
    done = {k: v for k, v in EL.items() if k in strings}
    write_mo(LANG / 'el.mo', done, 'Project-Id-Version: VASSILISDESIGN 1.0.0\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nLanguage: el\nPlural-Forms: nplurals=2; plural=(n != 1);\n')
    print(f'{len(strings)} strings extracted, {len(done)} translated to Greek')
    missing = [k for k in EL if k not in strings]
    if missing:
        print('not found in source:', missing)
