import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, ActivityIndicator } from 'react-native';
import { fetchTodayPanchangam } from '../../api/panchangam';
import { useRouter } from 'expo-router';

export default function Home() {
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      const res = await fetchTodayPanchangam();
      if (res.success) {
        setData(res.data);
      }
      setLoading(false);
    };
    loadData();
  }, []);

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <View style={styles.header}>
        <Text style={styles.title}>Dharma Jyothi Vedika</Text>
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Today's Panchangam</Text>
        {loading ? (
          <ActivityIndicator size="small" color="#7A2419" />
        ) : data ? (
          <View>
            <Text style={styles.cardText}>Date: {data.date}</Text>
            <Text style={styles.cardText}>Tithi: {data.panchangam?.tithi?.nameTe} / {data.panchangam?.tithi?.name}</Text>
            <Text style={styles.cardText}>Nakshatra: {data.panchangam?.nakshatra?.nakshatra?.nameTe} / {data.panchangam?.nakshatra?.nakshatra?.name}</Text>
            <Text style={styles.cardText}>Vara: {data.panchangam?.vara?.nameTe} / {data.panchangam?.vara?.en}</Text>
          </View>
        ) : (
          <Text style={styles.cardText}>Failed to load Panchangam.</Text>
        )}
        <TouchableOpacity style={styles.button} onPress={() => router.push('/panchangam')}>
          <Text style={styles.buttonText}>View Details</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#FFF9F0' },
  content: { padding: 16 },
  header: { marginBottom: 24, alignItems: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#7A2419', marginBottom: 8 },
  card: {
    backgroundColor: '#fff', borderRadius: 12, padding: 20, marginBottom: 24,
    shadowColor: '#7A2419', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.1, shadowRadius: 12, elevation: 4,
    borderColor: '#E8D5C4', borderWidth: 1,
  },
  cardTitle: { fontSize: 20, fontWeight: 'bold', color: '#2C1A14', marginBottom: 12 },
  cardText: { fontSize: 16, color: '#6B4C3B', marginBottom: 8 },
  button: { backgroundColor: '#C89432', paddingVertical: 12, paddingHorizontal: 20, borderRadius: 8, alignItems: 'center', marginTop: 12 },
  buttonText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
});
