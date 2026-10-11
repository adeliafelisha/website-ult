import html
import json
import os
import re
import shutil
import sys
import zipfile
import xml.etree.ElementTree as ET
from pathlib import Path

W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
R = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
A = "http://schemas.openxmlformats.org/drawingml/2006/main"
PR = "http://schemas.openxmlformats.org/package/2006/relationships"
NS = {"w": W, "r": R, "a": A}
URL_PATTERN = re.compile(r"(?<![\"'=])(https?://[^\s<]+|(?<!@)\b(?:smup|admission|students|parents)\.unpad\.ac\.id(?:/[^\s<]*)?|\b[\w.+-]+@unpad\.ac\.id)", re.I)


def slugify(value):
    value = value.lower().replace("&", " dan ")
    value = re.sub(r"[^a-z0-9]+", "-", value)
    return value.strip("-")


def normalize_text(value):
    return " ".join(value.replace("�", '"').split())


def linkify(value):
    escaped = html.escape(normalize_text(value))

    def replace(match):
        label = match.group(0).rstrip(".,)")
        suffix = match.group(0)[len(label):]
        if "@" in label and not label.lower().startswith("http"):
            href = "mailto:" + label
        elif label.lower().startswith("http"):
            href = label
        else:
            href = "https://" + label
        return f'<a href="{html.escape(href, quote=True)}">{html.escape(label)}</a>{suffix}'

    return URL_PATTERN.sub(replace, escaped)


def relationships(archive):
    root = ET.fromstring(archive.read("word/_rels/document.xml.rels"))
    return {
        rel.attrib["Id"]: rel.attrib["Target"]
        for rel in root.findall(f"{{{PR}}}Relationship")
    }


def cell_text(cell):
    return normalize_text("".join(node.text or "" for node in cell.findall(".//w:t", NS)))


def procedure_html(cell, rels, archive, asset_directory, public_prefix, service_slug):
    blocks = []
    image_index = 0
    source_to_public = {}
    urls = []

    for paragraph in cell.findall(".//w:p", NS):
        pieces = []
        for child in paragraph.iter():
            if child.tag == f"{{{W}}}t" and child.text:
                pieces.append(("text", child.text))
            elif child.tag == f"{{{W}}}tab":
                pieces.append(("text", " "))
            elif child.tag == f"{{{W}}}br":
                pieces.append(("text", "\n"))
            elif child.tag == f"{{{A}}}blip":
                rel_id = child.attrib.get(f"{{{R}}}embed")
                target = rels.get(rel_id)
                if target:
                    pieces.append(("image", target))

        text_parts = []
        for kind, value in pieces:
            if kind == "text":
                text_parts.append(value)
                continue
            text_value = "".join(text_parts).strip()
            if text_value:
                blocks.append(f"<p>{linkify(text_value)}</p>")
                urls.extend(extract_urls(text_value))
            text_parts = []
            if value not in source_to_public:
                image_index += 1
                extension = Path(value).suffix.lower()
                filename = f"{service_slug}-{image_index:02d}{extension}"
                source_to_public[value] = f"{public_prefix}/{filename}"
                with archive.open("word/" + value) as source, open(asset_directory / filename, "wb") as target:
                    shutil.copyfileobj(source, target)
            image_url = source_to_public[value]
            alt = html.escape(f"Petunjuk {service_slug.replace('-', ' ')} langkah {image_index}")
            blocks.append(f'<figure><img src="{image_url}" alt="{alt}" loading="lazy"></figure>')

        text_value = "".join(text_parts).strip()
        if text_value:
            blocks.append(f"<p>{linkify(text_value)}</p>")
            urls.extend(extract_urls(text_value))

    return "\n".join(blocks), list(dict.fromkeys(urls)), image_index


def extract_urls(value):
    results = []
    for match in URL_PATTERN.finditer(value):
        item = match.group(0).rstrip(".,)")
        if "@" in item and not item.lower().startswith("http"):
            item = "mailto:" + item
        elif not item.lower().startswith("http"):
            item = "https://" + item
        results.append(item)
    return results


def make_buttons(urls):
    buttons = []
    for url in urls[:3]:
        if url.startswith("mailto:"):
            label, channel = "Kirim Email", "email"
        else:
            host = re.sub(r"^https?://", "", url).split("/")[0]
            label, channel = f"Buka {host}", "website"
        buttons.append({"label": label, "label_en": label, "channel": channel, "url": url})
    return buttons


def parse_document(path, category, audience, sort_order, output_root):
    category_slug = slugify(category)
    asset_directory = output_root / "public" / "images" / "service-guides" / category_slug
    asset_directory.mkdir(parents=True, exist_ok=True)
    public_prefix = f"/images/service-guides/{category_slug}"
    records = []
    current_group = ""

    with zipfile.ZipFile(path) as archive:
        document = ET.fromstring(archive.read("word/document.xml"))
        rels = relationships(archive)
        table = document.find(".//w:tbl", NS)
        rows = table.findall("./w:tr", NS)[1:]
        for row in rows:
            cells = row.findall("./w:tc", NS)
            if len(cells) < 4:
                continue
            group = cell_text(cells[1])
            if group:
                current_group = group
            title = cell_text(cells[2])
            if not title:
                continue
            service_slug = f"{category_slug}-{slugify(title)}"
            procedure, urls, image_count = procedure_html(
                cells[3], rels, archive, asset_directory, public_prefix, service_slug
            )
            records.append({
                "category_slug": category_slug,
                "group": current_group,
                "title": title,
                "slug": service_slug,
                "summary": f"Informasi dan panduan {title} untuk {audience.lower()}.",
                "audience": audience,
                "procedure": procedure or "<p>Rincian layanan belum tersedia pada dokumen sumber.</p>",
                "delivery_type": "online" if urls else "hybrid",
                "responsible_unit": "Unit terkait — perlu verifikasi",
                "content_owner": "ULT Unpad",
                "contact_buttons": make_buttons(urls),
                "keywords": list(dict.fromkeys((category + " " + current_group + " " + title).lower().split())),
                "image_count": image_count,
                "is_featured": False,
                "is_published": True,
            })

    return {
        "category": {
            "name": category,
            "slug": category_slug,
            "description": f"Informasi layanan untuk {audience.lower()}.",
            "sort_order": sort_order,
            "is_featured": True,
        },
        "services": records,
    }


def main():
    if len(sys.argv) != 6:
        raise SystemExit("Usage: import_service_catalog_docx.py <calon.docx> <mahasiswa.docx> <alumni.docx> <disabilitas.docx> <project-root>")
    output_root = Path(sys.argv[5]).resolve()
    asset_root = output_root / "public" / "images" / "service-guides"
    if asset_root.exists():
        shutil.rmtree(asset_root)
    catalogs = [
        parse_document(sys.argv[1], "Calon Mahasiswa", "Calon mahasiswa", 0, output_root),
        parse_document(sys.argv[2], "Mahasiswa", "Mahasiswa", 1, output_root),
        parse_document(sys.argv[3], "Alumni", "Alumni", 2, output_root),
        parse_document(sys.argv[4], "Layanan Disabilitas", "Mahasiswa dan pengguna layanan disabilitas", 3, output_root),
    ]
    data_directory = output_root / "database" / "data"
    data_directory.mkdir(parents=True, exist_ok=True)
    output_path = data_directory / "service-catalog.json"
    output_path.write_text(json.dumps(catalogs, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Wrote {sum(len(item['services']) for item in catalogs)} services to {output_path}")
    print(f"Extracted {sum(service['image_count'] for item in catalogs for service in item['services'])} service images")


if __name__ == "__main__":
    main()
