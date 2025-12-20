import os
import time
import random
import csv
from datetime import datetime, timezone

import psycopg2
from psycopg2 import sql
import pandas as pd


CSV_DIR = os.getenv("CSV_OUT_DIR", "/data/csv")
XLSX_DIR = os.getenv("XLSX_OUT_DIR", "/data/xlsx")
PERIOD = int(os.getenv("GEN_PERIOD_SEC", "300"))

DB_PARAMS = {
    "host": os.getenv("PGHOST", "db"),
    "port": os.getenv("PGPORT", "5432"),
    "dbname": os.getenv("PGDATABASE", "monolith"),
    "user": os.getenv("PGUSER", "monouser"),
    "password": os.getenv("PGPASSWORD", "monopass"),
}

os.makedirs(CSV_DIR, exist_ok=True)
os.makedirs(XLSX_DIR, exist_ok=True)


def generate_data_row(filename):
    """Генерирует одну строку данных"""
    recorded_at = datetime.now(timezone.utc).replace(microsecond=0)
    voltage = round(random.uniform(3.2, 12.6), 2)
    temp = round(random.uniform(-50.0, 80.0), 2)

    telemetry_id = random.randint(100000, 999999)
    is_active = random.choice([True, False])

    statuses = ["NOMINAL", "DEGRADED", "OFFLINE", "MAINTENANCE"]
    mission_status = random.choice(statuses)

    return {
        "telemetry_id": telemetry_id,
        "recorded_at": recorded_at,
        "is_active": is_active,
        "voltage": voltage,
        "temp": temp,
        "mission_status": mission_status,
        "source_file": filename  # added
    }


def generate_csv():
    """Генерирует CSV-файл"""
    ts_file = datetime.now(timezone.utc).strftime("%Y%m%d_%H%M%S")
    filename = f"telemetry_{ts_file}.csv"
    filepath = os.path.join(CSV_DIR, filename)

    data = generate_data_row(filename)

    header = list(data.keys())
    row = []

    for key in header:
        value = data[key]

        if isinstance(value, datetime):
            value = value.replace(tzinfo=None).isoformat(sep=' ')
        elif isinstance(value, bool):
            value = 'TRUE' if value else 'FALSE'

        row.append(value)

    with open(filepath, "w", newline="") as f:
        writer = csv.writer(f)
        writer.writerow(header)
        writer.writerow(row)

    print(f"Generated CSV {filename}: ID={data['telemetry_id']}, Status={data['mission_status']}")
    return filepath, data


def generate_xlsx(data):
    """Генерирует XLSX-файл"""
    ts_file = datetime.now(timezone.utc).strftime("%Y%m%d_%H%M%S")
    filename = f"telemetry_{ts_file}.xlsx"
    filepath = os.path.join(XLSX_DIR, filename)

    data_for_xlsx = data.copy()
    if isinstance(data_for_xlsx.get("recorded_at"), datetime):
        data_for_xlsx["recorded_at"] = data_for_xlsx["recorded_at"].replace(tzinfo=None)

    df = pd.DataFrame([data_for_xlsx])

    try:
        df.to_excel(
            filepath,
            index=False,
            sheet_name="Telemetry Data",
            engine="xlsxwriter"
        )
        print(f"Generated XLSX {filename}")
    except Exception as e:
        print(f"XLSX generation error: {e}")


def copy_to_postgres(filepath):
    """Импортирует CSV в PostgreSQL"""
    temp_filepath = filepath + ".tmp"

    with open(filepath, "r", newline="") as infile, open(temp_filepath, "w", newline="") as outfile:
        reader = csv.reader(infile)
        writer = csv.writer(outfile)

        header = next(reader)
        writer.writerow(header)

        is_active_idx = header.index("is_active") if "is_active" in header else -1

        for row in reader:
            if is_active_idx != -1:
                if row[is_active_idx].upper() == "TRUE":
                    row[is_active_idx] = "t"
                elif row[is_active_idx].upper() == "FALSE":
                    row[is_active_idx] = "f"
            writer.writerow(row)

    conn = None
    try:
        conn = psycopg2.connect(**DB_PARAMS)
        cur = conn.cursor()

        col_sql = sql.SQL(", ").join(map(sql.Identifier, header))

        with open(temp_filepath, "r") as f:
            copy_cmd = sql.SQL("""
                COPY telemetry_legacy ({})
                FROM STDIN WITH (FORMAT csv, HEADER true)
            """).format(col_sql)

            cur.copy_expert(copy_cmd, f)

        conn.commit()
        print(f"Imported {os.path.basename(filepath)} into telemetry_legacy")

    except Exception as e:
        print(f"DB error: {e}")

    finally:
        if conn:
            conn.close()

        if os.path.exists(temp_filepath):
            os.remove(temp_filepath)

        if os.path.exists(filepath):
            os.remove(filepath)


if __name__ == "__main__":
    print(f"Telemetry legacy started, period={PERIOD}s")

    while True:
        try:
            csv_path, data = generate_csv()
            copy_to_postgres(csv_path)
            generate_xlsx(data)

        except Exception as e:
            print(f"Legacy error: {e}")

        time.sleep(PERIOD)
