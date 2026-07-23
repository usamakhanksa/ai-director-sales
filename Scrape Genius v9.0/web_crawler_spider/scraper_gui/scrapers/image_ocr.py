"""
scrapers/image_ocr.py
=======================
OCRs an uploaded image with Tesseract (via pytesseract) and extracts
emails/phones from the recognized text - the Python equivalent of the
Node backend's Tesseract.js image scraper.

Requires the Tesseract OCR binary to be installed on the host (not just
the `pytesseract`/`Pillow` Python packages) - if it's missing this returns
a clear error instead of a stack trace, per source_name so the UI can
explain the fix (e.g. `choco install tesseract` on Windows).
"""

import io

from PIL import Image
import pytesseract

from .utils import extract_emails, extract_phones


def extract_from_image(filename: str, raw: bytes) -> dict:
    try:
        image = Image.open(io.BytesIO(raw))
    except Exception as exc:
        return {"error": f"Could not read image file: {exc}"}

    try:
        text = pytesseract.image_to_string(image)
    except pytesseract.TesseractNotFoundError:
        return {
            "error": (
                "Tesseract OCR engine is not installed on this machine. "
                "Install it (e.g. https://github.com/UB-Mannheim/tesseract/wiki on Windows) "
                "and ensure it's on PATH, then retry."
            )
        }
    except Exception as exc:
        return {"error": f"OCR failed: {exc}"}

    return {
        "filename": filename,
        "text": text.strip(),
        "emails": extract_emails(text),
        "phones": extract_phones(text),
    }
