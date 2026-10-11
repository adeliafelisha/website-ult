from pathlib import Path

from openpyxl import load_workbook


path = Path(r"C:\Users\Ruri\Downloads\Tab _Website_.xlsx")
workbook = load_workbook(path, data_only=False)

print("SHEETS", workbook.sheetnames)
for worksheet in workbook.worksheets:
    print(f"\n### {worksheet.title} {worksheet.max_row} {worksheet.max_column}")
    print("MERGED", [str(cell_range) for cell_range in worksheet.merged_cells.ranges])
    for row in worksheet.iter_rows():
        values = [f"{cell.coordinate}={cell.value!r}" for cell in row if cell.value is not None]
        if values:
            print(" | ".join(values))
