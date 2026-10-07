import { View, Text, StyleSheet } from 'react-native';

export default function FestivalsScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Festivals</Text>
      <Text>Coming soon...</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16, backgroundColor: '#FFF9F0' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#7A2419' }
});
