"""
scrapers/document_scraper.py
==============================
Extracts emails/phones from an uploaded .txt, .csv, or .docx file.
No OCR/network involved - pure local parsing (Papa Parse/mammoth
equivalents: csv module + python-docx).
"""

import csv
import io
from typing import Tuple

from docx import Document

from .utils import extract_emails, extract_phones


def _read_txt(raw: bytes) -> str:
    return raw.decode("utf-8", errors="replace")


def _read_csv(raw: bytes) -> str:
    text = raw.decode("utf-8-sig", errors="replace")
    reader = csv.reader(io.StringIO(text))
    return "\n".join(", ".join(row) for row in reader)


def _read_docx(raw: bytes) -> str:
    doc = Document(io.BytesIO(raw))
    return "\n".join(p.text for p in doc.paragraphs)


_READERS = {
    "txt": _read_txt,
    "csv": _read_csv,
    "docx": _read_docx,
}


def extract_from_document(filename: str, raw: bytes) -> dict:
    ext = filename.rsplit(".", 1)[-1].lower() if "." in filename else ""
    reader = _READERS.get(ext)
    if not reader:
        return {"error": f"Unsupported file type '.{ext}'. Supported: .txt, .csv, .docx"}

    try:
        text = reader(raw)
    except Exception as exc:
        return {"error": f"Failed to parse file: {exc}"}

    return {
        "filename": filename,
        "emails": extract_emails(text),
        "phones": extract_phones(text),
        "char_count": len(text),
    }
