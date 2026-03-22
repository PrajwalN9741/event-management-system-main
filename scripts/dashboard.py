import streamlit as st
import pandas as pd
import plotly.express as px
import plotly.graph_objects as go
import os
import random
from datetime import datetime

st.set_page_config(page_title="Cyber Defense Command Center", layout="wide")

# ---------- STYLE ----------

st.markdown("""
<style>

body{
background-color:#020617;
color:white;
}

.metric-box{
padding:16px;
border-radius:10px;
background:#0f172a;
border:1px solid #1e293b;
text-align:center;
font-weight:bold;
}

.metric-box h1{
font-size:28px;
margin:0;
}

.metric-critical{background:#7f1d1d;}
.metric-high{background:#9a3412;}
.metric-medium{background:#a16207;}
.metric-low{background:#065f46;}

.ticker{
background:#111827;
padding:8px;
border-radius:6px;
font-size:14px;
margin-bottom:10px;
}

</style>
""", unsafe_allow_html=True)

# ---------- LOAD DATA ----------

data_path = os.path.join(os.path.dirname(__file__), "..", "database", "alerts_log.csv")

df = pd.read_csv(data_path)
df["Timestamp"] = pd.to_datetime(df["Timestamp"])

# ---------- SESSION STORAGE FOR SIMULATION ----------

if "simulated_alerts" not in st.session_state:
    st.session_state.simulated_alerts = pd.DataFrame(columns=df.columns)

# combine real + simulated
df = pd.concat([df, st.session_state.simulated_alerts], ignore_index=True)

attack_df = df[df["Attack"] != "BENIGN"]

# ---------- HEADER ----------

st.title("CYBER DEFENSE COMMAND CENTER")
st.caption("AI-Driven SOC Threat Intelligence Platform")

# ---------- LIVE TICKER ----------

recent = attack_df.tail(12)["Attack"].tolist()
ticker = " | ".join(recent)

st.markdown(
f'<div class="ticker">⚡ LIVE THREAT STREAM → {ticker}</div>',
unsafe_allow_html=True
)

# ---------- METRICS ----------

total=len(df)
critical=len(df[df["Priority"]=="Critical"])
high=len(df[df["Priority"]=="High"])
medium=len(df[df["Priority"]=="Medium"])
low=len(df[df["Priority"]=="Low"])

c1,c2,c3,c4,c5 = st.columns(5)

c1.markdown(f'<div class="metric-box">TOTAL<h1>{total}</h1></div>',unsafe_allow_html=True)
c2.markdown(f'<div class="metric-box metric-critical">CRITICAL<h1>{critical}</h1></div>',unsafe_allow_html=True)
c3.markdown(f'<div class="metric-box metric-high">HIGH<h1>{high}</h1></div>',unsafe_allow_html=True)
c4.markdown(f'<div class="metric-box metric-medium">MEDIUM<h1>{medium}</h1></div>',unsafe_allow_html=True)
c5.markdown(f'<div class="metric-box metric-low">LOW<h1>{low}</h1></div>',unsafe_allow_html=True)

# ---------- THREAT LEVEL ----------

score=(critical*4)+(high*3)+(medium*2)

if score>300:
    level="CRITICAL"
    color="#dc2626"
elif score>150:
    level="HIGH"
    color="#ea580c"
else:
    level="NORMAL"
    color="#16a34a"

st.markdown(
f"""
<div style="background:{color};
padding:12px;
border-radius:6px;
text-align:center;
font-size:20px;
font-weight:bold;">
🚨 GLOBAL SOC THREAT LEVEL : {level}
</div>
""",
unsafe_allow_html=True
)

# ---------- ATTACK SIMULATION PANEL ----------

st.subheader("Attack Simulation Panel")

col_sim1, col_sim2, col_sim3 = st.columns(3)

def simulate_attack(name, priority, mitre, risk):

    new_alert = {
        "AlertID": random.randint(9000,9999),
        "Attack": name,
        "RiskScore": risk,
        "Priority": priority,
        "MITRE_Technique": mitre,
        "Timestamp": datetime.now()
    }

    st.session_state.simulated_alerts = pd.concat(
        [st.session_state.simulated_alerts, pd.DataFrame([new_alert])]
    )

with col_sim1:
    if st.button("Simulate Ransomware Attack"):
        simulate_attack("Ransomware Activity","Critical","Impact",95)

with col_sim2:
    if st.button("Simulate DDoS Attack"):
        simulate_attack("DDoS","High","Impact",85)

with col_sim3:
    if st.button("Simulate Data Exfiltration"):
        simulate_attack("Data Exfiltration","Critical","Exfiltration",92)

# ---------- MAIN ANALYTICS ----------

col1,col2,col3 = st.columns(3)

with col1:

    gauge_score=min(score,500)

    fig = go.Figure(go.Indicator(
        mode="gauge+number",
        value=gauge_score,
        title={'text':"Threat Score"},
        gauge={
            'axis':{'range':[None,500]},
            'bar':{'color':"red"}
        }
    ))

    st.plotly_chart(fig,use_container_width=True)

with col2:

    attack_counts = attack_df["Attack"].value_counts().head(8)

    fig2 = px.bar(
        attack_counts,
        color=attack_counts.values,
        color_continuous_scale="Reds",
        title="Attack Distribution"
    )

    st.plotly_chart(fig2,use_container_width=True)

with col3:

    priority_counts=df["Priority"].value_counts()

    fig3 = px.pie(
        values=priority_counts.values,
        names=priority_counts.index,
        title="Priority Distribution"
    )

    st.plotly_chart(fig3,use_container_width=True)

# ---------- TIMELINE ----------

timeline = df.set_index("Timestamp").resample("2min").size()

fig4 = px.line(
timeline,
title="Attack Timeline"
)

fig4.update_traces(line=dict(width=3,color="#38bdf8"))

st.plotly_chart(fig4,use_container_width=True)

# ---------- THREAT RADAR ----------

st.subheader("Threat Radar")

radar_data = attack_df["Attack"].value_counts().head(6)

fig_radar = go.Figure()

fig_radar.add_trace(go.Scatterpolar(
    r=radar_data.values,
    theta=radar_data.index,
    fill='toself',
    line=dict(color='red', width=3)
))

fig_radar.update_layout(
polar=dict(radialaxis=dict(visible=True)),
template="plotly_dark",
showlegend=False,
height=450
)

st.plotly_chart(fig_radar,use_container_width=True)

# ---------- GLOBAL ATTACK MAP ----------

st.subheader("Global Cyber Attack Flow")

fig_map = go.Figure()

for i in range(15):

    s_lat=random.uniform(-60,60)
    s_lon=random.uniform(-180,180)

    t_lat=random.uniform(-60,60)
    t_lon=random.uniform(-180,180)

    fig_map.add_trace(go.Scattergeo(
        lon=[s_lon,t_lon],
        lat=[s_lat,t_lat],
        mode='lines',
        line=dict(width=2,color='red'),
        opacity=0.7,
        showlegend=False
    ))

fig_map.update_layout(
height=500,
geo=dict(
projection_type="natural earth",
showland=True,
landcolor="#1f2937",
bgcolor="#020617"
)
)

st.plotly_chart(fig_map,use_container_width=True)
# ---------- LIVE ALERT FEED ----------

st.subheader("Live SOC Alert Feed")

st.dataframe(df.tail(40),use_container_width=True)

st.markdown("""
---
SOC Monitoring Platform  
AI-Driven SOC Alert Prioritization Framework
""")