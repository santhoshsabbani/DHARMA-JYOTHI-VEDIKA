import { View, Text, StyleSheet } from 'react-native';

export default function PoojaScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Pooja</Text>
      <Text>Coming soon...</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16, backgroundColor: '#FFF9F0' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#7A2419' }
});
