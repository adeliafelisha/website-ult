import os
import sys
import zipfile
import xml.etree.ElementTree as ET

W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
R = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
A = "http://schemas.openxmlformats.org/drawingml/2006/main"
PR = "http://schemas.openxmlformats.org/package/2006/relationships"
NS = {"w": W, "r": R, "a": A}


def paragraph_tokens(paragraph, relationships):
    tokens = []
    for node in paragraph.iter():
        if node.tag == f"{{{W}}}t" and node.text:
            tokens.append(node.text)
        elif node.tag == f"{{{W}}}tab":
            tokens.append("\t")
        elif node.tag == f"{{{W}}}br":
            tokens.append("\n")
        elif node.tag == f"{{{A}}}blip":
            rel_id = node.attrib.get(f"{{{R}}}embed")
            if rel_id in relationships:
                tokens.append(f"[IMAGE:{relationships[rel_id]}]")
    return "".join(tokens).strip()


def inspect(path):
    with zipfile.ZipFile(path) as archive:
        document = ET.fromstring(archive.read("word/document.xml"))
        rel_root = ET.fromstring(archive.read("word/_rels/document.xml.rels"))
        relationships = {
            rel.attrib["Id"]: rel.attrib["Target"]
            for rel in rel_root.findall(f"{{{PR}}}Relationship")
        }
        print(f"\n## {os.path.basename(path)}")
        table = document.find(".//w:tbl", NS)
        for row_index, row in enumerate(table.findall("./w:tr", NS)):
            print(f"\nROW {row_index}")
            for cell_index, cell in enumerate(row.findall("./w:tc", NS)):
                print(f"CELL {cell_index}")
                for paragraph in cell.findall(".//w:p", NS):
                    value = paragraph_tokens(paragraph, relationships)
                    if value:
                        print(value)


for document_path in sys.argv[1:]:
    inspect(document_path)
