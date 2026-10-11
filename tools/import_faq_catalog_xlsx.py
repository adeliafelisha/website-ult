import html
import json
import re
from pathlib import Path

from openpyxl import load_workbook


SOURCE = Path(r"C:\Users\Ruri\Downloads\Tab _Website_.xlsx")
OUTPUT = Path(__file__).resolve().parents[1] / "database" / "data" / "faq-catalog.json"

CATEGORY_EN = {
    "Informasi Umum Unit Layanan Terpadu (ULT) Unpad": "General Information about Unpad's Integrated Service Unit (ULT)",
    "Mahasiswa Baru": "New Students",
    "Mahasiswa Aktif": "Current Students",
    "Lulusan": "Graduates",
    "Biaya": "Fees",
}

# English copy is editorial content for the website, not machine translated at runtime.
TRANSLATIONS = {
    3: ("What services does Unpad's Integrated Service Unit (ULT) provide?", "ULT Unpad provides integrated services for the entire Unpad academic community, including students, lecturers, education staff, and the general public. ULT receives and handles various public service needs related to university administration and services, both in person and online."),
    4: ("What are Unpad ULT's operating hours?", "Unpad's Integrated Service Unit (ULT) operates Monday–Thursday from 8:30 a.m. to 4:00 p.m. WIB and Friday from 8:30 a.m. to 4:30 p.m. WIB."),
    5: ("Where is Unpad ULT located?", "Unpad's Integrated Service Unit (ULT) is located on the first floor of the Universitas Padjadjaran Rectorate Building."),
    7: ("How can I contact Unpad ULT?", "You can contact Unpad's Integrated Service Unit (ULT) through:\n\nUnpad Official WhatsApp: 0811-2230-1410\nULT Service WhatsApp: 0811-2230-1383 and 0811-2230-1384"),
    8: ("Does Unpad ULT provide disability services?", "Yes. Unpad's Integrated Service Unit (ULT) provides services for persons with disabilities, either in person at the ULT office or online through the Disability Service WhatsApp number at 0811-2230-1382."),
    10: ("Are ULT services subject to a fee?", "No. All ULT services are free of charge."),
    11: ("What is the procedure for receiving ULT services in person?", "To receive an in-person ULT service, visit the ULT office and take a queue number according to the service category you need. Wait until your number is called, then proceed to the designated counter to receive assistance from a ULT officer."),
    12: ("What happens if I do not complete new student registration according to the schedule and requirements?", "You will be considered to have withdrawn."),
    13: ("Can I change study programs after I have been admitted to Unpad?", "A change of study program is only permitted starting in the third or fourth semester."),
    14: ("Can regular SMUP applicants apply for KIP-K?", "No, they cannot."),
    15: ("How do I submit a withdrawal request from Unpad?", "Please send a letter by email to wr1@unpad.ac.id."),
    16: ("How can I obtain a Letter of Acceptance (LoA) from the university?", "Participants can download it from https://students.unpad.ac.id/peserta under the Download menu. If it does not appear, submit a request containing your name, participant number, and study program to dit.akademik@unpad.ac.id."),
    17: ("How do I request a study deferral for a postgraduate program?", "Successful applicants who require an LoA should submit a request containing their name, participant number, and study program to dit.akademik@unpad.ac.id."),
    18: ("Which admission pathways does Unpad offer?", "Prospective Universitas Padjadjaran students can apply through the SNMPTN, SBMPTN, or SMUP admission pathways. Information about new student admissions is available at https://www.smup.unpad.ac.id/."),
    19: ("How can I obtain a statement confirming that I am not receiving a scholarship?", "Please submit a request through your respective study program or faculty."),
    20: ("If I have been admitted through SNBP, can I register for SNBT or the Independent Admission Track?", "Students who are admitted through SNBP cannot register for new student admission through SNBT or the Independent Admission Track."),
    21: ("I forgot my PAuS Name. How can I find it?", "If you forget your Universitas Padjadjaran PAuS account, recover your PAuS Name at https://paus.unpad.ac.id/forgot/paus-name/ or reset your password at https://paus.unpad.ac.id/forgot/password/. A notification will be sent by email or SMS using the details you provided. If you have not provided a mobile phone number and email address, submit a service ticket at https://siat.unpad.ac.id/layanan and include your full name and student ID number (NPM)."),
    22: ("Can I request a reduction in UKT and IPI?", "UKT and IPI reductions are not available for the Independent Admission Track."),
    23: ("I am a new student and a Unpad alumnus. Do I need to create a new PAuS ID?", "No. Use your existing PAuS Name, open the Validation menu, select Additional Registration, choose Student Account, and upload your new student ID card (KTM)."),
    24: ("How do I create a PAuS ID?", "Register at https://paus.unpad.ac.id/registration. For further guidance, visit https://support.unpad.ac.id/how-to/paus-registrasi/paus-registrasi-mahasiswa/."),
    25: ("What should I do if I am late paying UKT or submitting my study plan (KRS)?", "Visit or contact your study program to obtain a recommendation letter for reopening the tuition bill and arranging a late KRS submission if you missed the UKT payment or KRS deadline."),
    26: ("How do I replace a lost or damaged student ID card (KTM)?", "Visit the issuing bank and bring a loss report from the relevant authority."),
    27: ("When is the re-registration payment period?", "For the even semester, re-registration payments can be made in January. For the odd semester, payments can be made in July."),
    28: ("How do I apply for academic leave?", "To apply for academic leave at Unpad:\n1. Submit an academic leave request letter to the Head of the Study Program through the study program's official email address. The letter template is usually available on the relevant faculty website.\n2. The study program will verify your active status and forward the request to the Vice Dean for approval.\n3. Once approved, you will receive an Academic Leave Decree.\n4. Academic leave is generally limited by semester and is not permitted during the first two semesters of study.\n5. Additional requirements and procedures may vary by faculty or study program."),
    29: ("Where can I find information about scholarships at Unpad?", "Information about scholarships at Unpad is available at https://www.beasiswa.unpad.ac.id/."),
    30: ("How do I request a change of study program?", "Submit a request addressed to the Head of your Study Program. The study program will forward the letter to the faculty, which will then forward it to the university."),
    31: ("How do I withdraw from the university?", "Visit or contact your respective study program."),
    32: ("How can I obtain an active student status letter?", "Submit a request through your respective study program. The faculty issues the active student status letter."),
    33: ("How do I legalize my diploma or academic transcript?", "Contact the academic administration office of your respective faculty."),
    34: ("Where can I collect my diploma after graduation?", "Graduates can collect their diploma from Unpad's Integrated Service Unit. Bring a diploma and transcript collection letter from your respective faculty."),
    35: ("Can graduation gowns be purchased?", "No. Universitas Padjadjaran does not sell graduation gowns. Gowns are only available on loan according to the applicable graduation schedule."),
    36: ("Where can I find information about graduation periods?", "Information about graduation periods is available on Instagram at @universitaspadjadjaran."),
    37: ("What is the process for borrowing a graduation gown?", "Information about the gown borrowing procedure and schedule is available in posts on Instagram at @universitaspadjadjaran."),
    38: ("Can someone else collect my diploma and transcript on my behalf?", "Bring a diploma and transcript collection letter from your respective faculty. If collection is delegated to another person, you must prepare a power of attorney bearing a Rp10,000 duty stamp and copies of the identity cards of both the person granting and the person receiving the authorization."),
    39: ("Is there a standard power of attorney template?", "No standard template is provided. You may use your own format."),
    40: ("I lost my diploma or transcript. Can it be reissued?", "Contact your respective faculty to request a duplicate diploma or transcript."),
    41: ("My name does not appear on PDDIKTI. What should I do?", "Universitas Padjadjaran provides the following PDDIKTI data services:\n\nChanges to primary student or alumni data\nRegistration of students or alumni in PDDIKTI\nUpdates to student or alumni course data\nChanges to the type of student or alumni exit status\nUpdates to the completeness of student or alumni graduation status\nAlumni statement letters in PDDIKTI\n\nTo continue the process, complete the form at https://bit.ly/LayananPDDIKTIUnpad. The form will be attached to the university's submission letter to DIKTI. Make sure that the information you provide is accurate and complete."),
    42: ("Is my diploma ready for collection?", "Your diploma is ready for collection if the final diploma and transcript, rather than draft versions, are available for download from your students account."),
}

FACULTY_EN = {
    "Hukum": "Law",
    "Ekonomi dan Bisnis": "Economics and Business",
    "Kedokteran": "Medicine",
    "Matematika dan Ilmu Pengetahuan Alam": "Mathematics and Natural Sciences",
    "Pertanian": "Agriculture",
    "Kedokteran Gigi": "Dentistry",
    "Ilmu Sosial dan Ilmu Politik": "Social and Political Sciences",
    "Ilmu Budaya": "Cultural Sciences",
    "Psikologi": "Psychology",
    "Peternakan": "Animal Husbandry",
    "Ilmu Komunikasi": "Communication Sciences",
    "Keperawatan": "Nursing",
    "Perikanan dan Ilmu Kelautan": "Fisheries and Marine Sciences",
    "Teknologi Industri Pertanian": "Agro-Industrial Technology",
    "Farmasi": "Pharmacy",
    "Teknik Geologi": "Geological Engineering",
}


def normalize_indonesian(value: str) -> str:
    value = value.replace("�", "–").strip()
    value = re.sub(r"^pakah\b", "Apakah", value)
    return value


def translate_fee_question(question: str) -> str:
    match = re.search(r"Program Sarjana (.+?) di Universitas Padjadjaran", question)
    subject = match.group(1) if match else ""
    if subject.startswith("Fakultas "):
        name = subject.removeprefix("Fakultas ")
        subject_en = f"the Faculty of {FACULTY_EN.get(name, name)} undergraduate program"
    elif subject == "Sekolah Vokasi":
        subject_en = "the Vocational School undergraduate program"
    elif subject == "PSDKU":
        subject_en = "the PSDKU undergraduate program"
    else:
        subject_en = f"the {FACULTY_EN.get(subject, subject)} undergraduate program"
    return f"What are the Single Tuition Fee (UKT) rates for {subject_en} at Universitas Padjadjaran?"


def translate_fee_answer(answer: str) -> str:
    translated = answer
    replacements = [
        ("Sesuai Dengan Keputusan Rektor Universitas Padjadjaran Nomor:", "In accordance with Universitas Padjadjaran Rector's Decree Number:"),
        ("Berikut rincian besaran UKT Program Sarjana", "The UKT rates for the undergraduate program of"),
        (" di Universitas Padjadjaran:", " at Universitas Padjadjaran are:"),
        ("Kelompok", "Group"),
        ("UKT Jalur Mandiri", "UKT – Independent Admission Track"),
        ("UKT IUP/Internasional", "UKT – IUP/International Program"),
        ("UKT Kelas Kerjasama", "UKT – Collaborative Program"),
        ("Iuran Pengembangan Institusi", "Institutional Development Fee"),
        ("Tiap Prodi berbeda", "varies by study program"),
        ("Tiap Prodi Berbeda", "varies by study program"),
        ("s.d.", "to"),
        ("s.d", "to"),
    ]
    for source, target in replacements:
        translated = translated.replace(source, target)
    for source, target in FACULTY_EN.items():
        translated = translated.replace(f"Fakultas {source}", f"the Faculty of {target}")
    translated = translated.replace("Sekolah Vokasi", "the Vocational School").replace("PSDKU", "PSDKU")
    return translated


def to_html(value: str) -> str:
    escaped = html.escape(value.strip())
    def link_url(match: re.Match) -> str:
        matched = match.group(1)
        url = matched.rstrip(".,;)")
        suffix = matched[len(url):]
        return f'<a href="{url}" target="_blank" rel="noopener">{url}</a>{suffix}'

    escaped = re.sub(r"(https?://[^\s<]+)", link_url, escaped)
    escaped = re.sub(r"(?<![\w@>])([\w.+-]+@[\w.-]+\.[A-Za-z]{2,})", r'<a href="mailto:\1">\1</a>', escaped)
    paragraphs = [part.strip().replace("\n", "<br>") for part in re.split(r"\n\s*\n", escaped) if part.strip()]
    return "".join(f"<p>{part}</p>" for part in paragraphs)


workbook = load_workbook(SOURCE, data_only=True)
worksheet = workbook["Website"]
catalog = []
seen_questions = set()
category = None

for row_number in range(3, 61):
    category = normalize_indonesian(str(worksheet.cell(row_number, 1).value)) if worksheet.cell(row_number, 1).value else category
    question = normalize_indonesian(str(worksheet.cell(row_number, 2).value or ""))
    answer = normalize_indonesian(str(worksheet.cell(row_number, 3).value or ""))
    duplicate_key = re.sub(r"\s+", " ", question.casefold()).strip()
    if not question or not answer or duplicate_key in seen_questions:
        continue
    seen_questions.add(duplicate_key)

    if row_number >= 43:
        question_en = translate_fee_question(question)
        answer_en = translate_fee_answer(answer)
    else:
        question_en, answer_en = TRANSLATIONS[row_number]

    catalog.append({
        "source_row": row_number,
        "category": category,
        "category_en": CATEGORY_EN[category],
        "question": question,
        "question_en": question_en,
        "answer": to_html(answer),
        "answer_en": to_html(answer_en),
        "sort_order": len(catalog),
        "is_featured": row_number in {3, 4, 7, 8, 12, 25},
        "is_published": True,
    })

OUTPUT.parent.mkdir(parents=True, exist_ok=True)
OUTPUT.write_text(json.dumps(catalog, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
print(f"Wrote {len(catalog)} unique FAQs to {OUTPUT}")
for name in CATEGORY_EN:
    print(name, sum(item["category"] == name for item in catalog))
