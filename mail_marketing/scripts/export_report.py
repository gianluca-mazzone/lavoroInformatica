import pymysql
import csv

conn = pymysql.connect(
    host='sql213.infinityfree.com',
    user='if0_39043708',
    password='nqODCBJsf8GZG',
    database='if0_39043708_mail_marketing'
)

with conn.cursor() as cur, open('report_transizioni.csv', 'w', newline='', encoding='utf-8') as f:
    cur.execute("""
        SELECT c.nome_campagna, t.stato_precedente, t.stato_successivo, COUNT(*) as tot
        FROM Transizioni t
        JOIN Campagna c ON t.id_campagna = c.ID_Campagna
        GROUP BY c.nome_campagna, t.stato_precedente, t.stato_successivo
    """)
    writer = csv.writer(f)
    writer.writerow(['Campagna', 'Stato Precedente', 'Stato Successivo', 'Totale'])
    for row in cur.fetchall():
        writer.writerow(row)
print("Report esportato in report_transizioni.csv")
