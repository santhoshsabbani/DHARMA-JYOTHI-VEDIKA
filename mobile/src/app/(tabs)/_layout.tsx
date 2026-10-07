import { Tabs } from 'expo-router';
import { StyleSheet } from 'react-native';

export default function TabLayout() {
  return (
    <Tabs
      screenOptions={{
        headerStyle: {
          backgroundColor: '#7A2419',
        },
        headerTintColor: '#E7B75A',
        headerTitleStyle: {
          fontWeight: 'bold',
        },
        tabBarActiveTintColor: '#7A2419',
        tabBarInactiveTintColor: '#888',
        tabBarStyle: {
          backgroundColor: '#FFF9F0',
          borderTopColor: '#E8D5C4',
        }
      }}>
      <Tabs.Screen
        name="index"
        options={{
          title: 'Home',
          tabBarLabel: 'Home',
        }}
      />
      <Tabs.Screen
        name="panchangam"
        options={{
          title: 'Panchangam',
          tabBarLabel: 'Panchangam',
        }}
      />
      <Tabs.Screen
        name="festivals"
        options={{
          title: 'Festivals',
          tabBarLabel: 'Festivals',
        }}
      />
      <Tabs.Screen
        name="pooja"
        options={{
          title: 'Pooja',
          tabBarLabel: 'Pooja',
        }}
      />
      <Tabs.Screen
        name="more"
        options={{
          title: 'More',
          tabBarLabel: 'More',
        }}
      />
    </Tabs>
  );
}
