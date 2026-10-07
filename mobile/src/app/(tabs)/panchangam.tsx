import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator } from 'react-native';
import { fetchPanchangam } from '../../api/panchangam';

export default function PanchangamScreen() {
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      const res = await fetchPanchangam();
      if (res.success) {
        setData(res.data);
      }
      setLoading(false);
    };
    loadData();
  }, []);

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <Text style={styles.title}>Detailed Panchangam</Text>
      {loading ? (
        <ActivityIndicator size="large" color="#7A2419" />
      ) : data ? (
        <View style={styles.card}>
          <Text style={styles.cardText}><Text style={styles.bold}>Tithi:</Text> {data.tithi?.name}</Text>
          <Text style={styles.cardText}><Text style={styles.bold}>Nakshatra:</Text> {data.nakshatra?.nakshatra?.name}</Text>
          <Text style={styles.cardText}><Text style={styles.bold}>Yoga:</Text> {data.yoga?.name}</Text>
          <Text style={styles.cardText}><Text style={styles.bold}>Karana:</Text> {data.karana?.name}</Text>
          <Text style={styles.cardText}><Text style={styles.bold}>Vara:</Text> {data.vara?.name}</Text>
          <Text style={styles.cardText}><Text style={styles.bold}>Sunrise:</Text> {new Date(data.solar?.sunrise).toLocaleTimeString()}</Text>
          <Text style={styles.cardText}><Text style={styles.bold}>Sunset:</Text> {new Date(data.solar?.sunset).toLocaleTimeString()}</Text>
        </View>
      ) : (
        <Text style={styles.errorText}>Could not load Panchangam data.</Text>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#FFF9F0' },
  content: { padding: 16 },
  title: { fontSize: 24, fontWeight: 'bold', color: '#7A2419', marginBottom: 16 },
  card: { backgroundColor: '#fff', borderRadius: 12, padding: 20, borderColor: '#E8D5C4', borderWidth: 1 },
  cardText: { fontSize: 16, color: '#6B4C3B', marginBottom: 8 },
  bold: { fontWeight: 'bold', color: '#2C1A14' },
  errorText: { color: 'red', textAlign: 'center' },
});
